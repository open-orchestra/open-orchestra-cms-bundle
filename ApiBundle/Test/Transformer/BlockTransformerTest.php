<?php

namespace OpenOrchestra\ApiBundle\Test\Transformer;

use Phake;
use OpenOrchestra\ApiBundle\Transformer\BlockTransformer;

/**
 * Class BlockTransformerTest
 */
class BlockTransformerTest extends \PHPUnit_Framework_TestCase
{
    protected $blockParameterManager;
    protected $generateFormManager;
    protected $displayBlockManager;
    protected $displayIconManager;
    protected $transformerManager;
    protected $blockTransformer;
    protected $nodeRepository;
    protected $blockFacade;
    protected $blockClass;
    protected $router;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->blockClass = 'OpenOrchestra\ModelBundle\Document\Block';
        $this->displayBlockManager = Phake::mock('OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager');
        $this->displayIconManager = Phake::mock('OpenOrchestra\BackofficeBundle\DisplayIcon\DisplayManager');
        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $this->blockFacade = Phake::mock('OpenOrchestra\ApiBundle\Facade\BlockFacade');
        $this->blockParameterManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\BlockParameterManager');
        $this->generateFormManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager');
        Phake::when($this->generateFormManager)->getDefaultConfiguration(Phake::anyParameters())->thenReturn(array());

        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');

        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn('route');
        $this->transformerManager = Phake::mock('OpenOrchestra\ApiBundle\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->router);

        $this->blockTransformer = new BlockTransformer($this->displayBlockManager, $this->displayIconManager, $this->blockClass, $this->blockParameterManager, $this->generateFormManager, $this->nodeRepository);
        $this->blockTransformer->setContext($this->transformerManager);
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $name = $this->blockTransformer->getName();

        $this->assertSame('block', $name);
    }

    /**
     * Test transform
     *
     * @param string     $component
     * @param array      $attributes
     * @param string     $label
     * @param array|null $expectedAttributes
     *
     * @dataProvider blockTransformProvider
     */
    public function testTransform(
        $component,
        $attributes,
        $label = null,
        $expectedAttributes = null
    )
    {
        $html = 'ok';
        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $response = Phake::mock('Symfony\Component\HttpFoundation\Response');
        $transformer = Phake::mock('OpenOrchestra\ApiBundle\Transformer\TransformerInterface');
        $transformerManager = Phake::mock('OpenOrchestra\ApiBundle\Transformer\TransformerManager');
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\UiModelFacade');

        Phake::when($block)->getComponent()->thenReturn($component);
        Phake::when($block)->getLabel()->thenReturn($label);
        Phake::when($block)->getAttributes()->thenReturn($attributes);
        Phake::when($this->displayBlockManager)->show($block)->thenReturn($response);
        Phake::when($response)->getContent()->thenReturn($html);
        Phake::when($this->displayIconManager)->show($component)->thenReturn('icon');

        $this->blockTransformer->setContext($transformerManager);

        Phake::when($transformerManager)->get('ui_model')->thenReturn($transformer);
        Phake::when($transformer)->transform(Phake::anyParameters())->thenReturn($facade);
        Phake::when($transformerManager)->getRouter()->thenReturn($this->router);

        $facadeExcepted = $this->blockTransformer->transform($block, true, 'root', 0);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\BlockFacade', $facadeExcepted);
        $this->assertSame($component, $facadeExcepted->component);
        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\UiModelFacade', $facadeExcepted->uiModel);
        $this->assertArrayHasKey('_self_form', $facadeExcepted->getLinks());
        if (is_null($expectedAttributes)) {
            $expectedAttributes = $attributes;
        }
        $this->assertSame($expectedAttributes, $facadeExcepted->getAttributes());
        Phake::verify($this->router)->generate(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function blockTransformProvider()
    {
        return array(
            array('sample', array('title' => 'title one', 'author' => 'me'), 'Sample'),
            array('sample', array('title' => 'news', 'author' => 'benj', 'text' => 'Hello world'), 'Sample'),
            array('news', array('title' => 'news', 'author' => 'benj', 'text' => 'Hello everybody'), 'News'),
            array('menu', array(), 'Menu'),
            array('menu', array('array' => array('test' => 'test')), 'Menu', array('array' => '{"test":"test"}')),
            array('menu', array(), null),
        );
    }

    /**
     * @param string $nodeId
     * @param array  $result
     * @param string $facadeNodeId
     * @param int    $blockId
     * @param array  $blockParameter
     *
     * @dataProvider blockReverseTransformProvider
     */
    public function testReverseTransformToArray($nodeId, $result, $facadeNodeId, $blockId, array $blockParameter = array())
    {
        $this->blockFacade->nodeId = $facadeNodeId;
        $this->blockFacade->blockId = $blockId;

        $nodeTransverse = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        Phake::when($this->node)->getNodeId()->thenReturn($nodeId);
        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($this->node)->getBlock(Phake::anyParameters())->thenReturn($block);
        Phake::when($nodeTransverse)->getBlock(Phake::anyParameters())->thenReturn($block);
        Phake::when($this->blockParameterManager)->getBlockParameter(Phake::anyParameters())->thenReturn($blockParameter);
        Phake::when($this->nodeRepository)->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($facadeNodeId, null)->thenReturn($nodeTransverse);

        $expected = $this->blockTransformer->reverseTransformToArray($this->blockFacade, $this->node);

        $this->assertSame($result, $expected);
    }

    /**
     * @return array
     */
    public function blockReverseTransformProvider()
    {
        return array(
            array('fixture_full', array('blockParameter' => array(), 'blockId' => 5, 'nodeId' => 0), 'fixture_full', 5),
            array('fixture_full', array('blockParameter' => array(), 'blockId' => 0, 'nodeId' => 0), 'fixture_full', 0),
            array('fixture_about_us', array('blockParameter' => array(), 'blockId' => 3, 'nodeId' => 'fixture_full'), 'fixture_full', 3),
            array('fixture_about_us', array('blockParameter' => array('newsId'), 'blockId' => 3, 'nodeId' => 'fixture_full'), 'fixture_full', 3, array('newsId')),
        );
    }

    /**
     * @param array  $result
     * @param string $component
     * @param int    $blockId
     * @param array  $blockParameter
     *
     * @dataProvider blockReverseTransformProvider2
     */
    public function testReverseTransformToArrayComponent($result, $component, $blockId, array $blockParameter = array())
    {
        $this->blockFacade->component = $component;
        Phake::when($this->node)->getBlockIndex(Phake::anyParameters())->thenReturn($blockId);
        Phake::when($this->blockParameterManager)->getBlockParameter(Phake::anyParameters())->thenReturn($blockParameter);

        $expected = $this->blockTransformer->reverseTransformToArray($this->blockFacade, $this->node);

        $this->assertSame($result, $expected);
        Phake::verify($this->node)->addBlock(Phake::anyParameters());
        Phake::verify($this->generateFormManager)->getDefaultConfiguration(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function blockReverseTransformProvider2()
    {
        return array(
            array(array('blockParameter' => array(), 'blockId' => 2, 'nodeId' => 0), 'sample', 2),
            array(array('blockParameter' => array(), 'blockId' => 3, 'nodeId' => 0), 'menu', 3),
            array(array('blockParameter' => array('newsId'), 'blockId' => 3, 'nodeId' => 0), 'news', 3, array('newsId')),
        );
    }

    /**
     * @param array  $result
     * @param string $facadeNodeId
     * @param int    $blockId
     *
     * @dataProvider blockReverseTransformProviderWithoutNode
     */
    public function testReverseTransformWithoutNode($result, $facadeNodeId, $blockId)
    {
        $this->blockFacade->nodeId = $facadeNodeId;
        $this->blockFacade->blockId = $blockId;

        $expected = $this->blockTransformer->reverseTransformToArray($this->blockFacade);

        $this->assertSame($result, $expected);
    }

    /**
     * @return array
     */
    public function blockReverseTransformProviderWithoutNode()
    {
        return array(
            array(array('blockParameter' => array(), 'blockId' => 0, 'nodeId' => 'fixture_full'), 'fixture_full', 0),
            array(array('blockParameter' => array(), 'blockId' => 3, 'nodeId' => 'fixture_full'), 'fixture_full', 3),
        );
    }

    /**
     * @param string $component
     * @param int    $blockIndex
     * @param array  $blockParameter
     *
     * @dataProvider provideComponentAndBlockIndex
     */
    public function testReverseTransformWithComponent($component, $blockIndex, array $blockParameter = array())
    {
        $this->blockFacade->component = $component;
        Phake::when($this->node)->getBlockIndex(Phake::anyParameters())->thenReturn($blockIndex);
        Phake::when($this->blockParameterManager)->getBlockParameter(Phake::anyParameters())->thenReturn($blockParameter);

        $result = $this->blockTransformer->reverseTransformToArray($this->blockFacade, $this->node);

        $this->assertSame(array('blockParameter' => $blockParameter, 'blockId' => $blockIndex, 'nodeId' => 0), $result);
        Phake::verify($this->node)->addBlock(Phake::anyParameters());
        Phake::verify($this->node)->getBlockIndex(Phake::anyParameters());
        Phake::verify($this->generateFormManager)->getDefaultConfiguration(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideComponentAndBlockIndex()
    {
        return array(
            array('Sample', 1),
            array('TinyMCE', 2),
            array('Carrossel', 0),
            array('Carrossel', 0, array('page', 'width')),
            array('News', 1),
            array('News', 1, array('newsId')),
        );
    }
}
