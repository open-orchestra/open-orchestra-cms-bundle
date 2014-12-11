<?php

namespace PHPOrchestra\ApiBundle\Test\Transformer;

use Phake;
use PHPOrchestra\ApiBundle\Transformer\BlockTransformer;

/**
 * Class BlockTransformerTest
 */
class BlockTransformerTest extends \PHPUnit_Framework_TestCase
{
    protected $displayBlockManager;
    protected $displayIconManager;
    protected $transformerManager;
    protected $blockTransformer;
    protected $blockFacade;
    protected $router;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->displayBlockManager = Phake::mock('PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager');
        $this->displayIconManager = Phake::mock('PHPOrchestra\BackofficeBundle\DisplayIcons\DisplayIconManager');
        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        $this->blockFacade = Phake::mock('PHPOrchestra\ApiBundle\Facade\BlockFacade');

        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn('route');
        $this->transformerManager = Phake::mock('PHPOrchestra\ApiBundle\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->router);

        $this->blockTransformer = new BlockTransformer($this->displayBlockManager, $this->displayIconManager);
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
        $block = Phake::mock('PHPOrchestra\ModelBundle\Model\BlockInterface');
        $response = Phake::mock('Symfony\Component\HttpFoundation\Response');
        $transformer = Phake::mock('PHPOrchestra\ApiBundle\Transformer\TransformerInterface');
        $transformerManager = Phake::mock('PHPOrchestra\ApiBundle\Transformer\TransformerManager');
        $facade = Phake::mock('PHPOrchestra\ApiBundle\Facade\UiModelFacade');

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

        $this->assertInstanceOf('PHPOrchestra\ApiBundle\Facade\BlockFacade', $facadeExcepted);
        $this->assertSame($component, $facadeExcepted->component);
        $this->assertInstanceOf('PHPOrchestra\ApiBundle\Facade\UiModelFacade', $facadeExcepted->uiModel);
        $this->assertArrayHasKey('_self_form', $facadeExcepted->getLinks());
        if ($expectedAttributes) {
            $this->assertSame($expectedAttributes, $facadeExcepted->getAttributes());
        } else {
            $this->assertSame($attributes, $facadeExcepted->getAttributes());
        }
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
     *
     * @dataProvider blockReverseTransformProvider
     */
    public function testReverseTransformToArray($nodeId, $result, $facadeNodeId, $blockId)
    {
        $this->blockFacade->nodeId = $facadeNodeId;
        $this->blockFacade->blockId = $blockId;

        Phake::when($this->node)->getNodeId()->thenReturn($nodeId);

        $expected = $this->blockTransformer->reverseTransformToArray($this->blockFacade, $this->node);

        $this->assertSame($result, $expected);
    }

    /**
     * @return array
     */
    public function blockReverseTransformProvider()
    {
        return array(
            array(
                'fixture_full',
                array('blockId' => 5, 'nodeId' => 0),
                'fixture_full',
                5
            ),
            array(
                'fixture_full',
                array('blockId' => 0, 'nodeId' => 0),
                'fixture_full',
                0
            ),
            array(
                'fixture_about_us',
                array('blockId' => 3, 'nodeId' => 'fixture_full'),
                'fixture_full',
                3
            ),
        );
    }

    /**
     * @param array  $result
     * @param string $component
     * @param int    $blockId
     *
     * @dataProvider blockReverseTransformProvider2
     */
    public function testReverseTransformToArrayComponent($result, $component, $blockId)
    {
        $this->blockFacade->component = $component;
        Phake::when($this->node)->getBlockIndex(Phake::anyParameters())->thenReturn($blockId);

        $expected = $this->blockTransformer->reverseTransformToArray($this->blockFacade, $this->node);

        $this->assertSame($result, $expected);
        Phake::verify($this->node)->addBlock(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function blockReverseTransformProvider2()
    {
        return array(
            array(
                array('blockId' => 2, 'nodeId' => 0),
                'sample',
                2
            ),
            array(
                array('blockId' => 3, 'nodeId' => 0),
                'menu',
                3
            ),
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
            array(
                array('blockId' => 0, 'nodeId' => 'fixture_full'),
                'fixture_full',
                0
            ),
            array(
                array('blockId' => 3, 'nodeId' => 'fixture_full'),
                'fixture_full',
                3
            ),
        );
    }

    /**
     * @param string $component
     * @param int    $blockIndex
     *
     * @dataProvider provideComponentAndBlockIndex
     */
    public function testReverseTransformWithComponent($component, $blockIndex)
    {
        $this->blockFacade->component = $component;
        Phake::when($this->node)->getBlockIndex(Phake::anyParameters())->thenReturn($blockIndex);

        $result = $this->blockTransformer->reverseTransformToArray($this->blockFacade, $this->node);

        $this->assertSame(array('blockId' => $blockIndex, 'nodeId' => 0), $result);
        Phake::verify($this->node)->addBlock(Phake::anyParameters());
        Phake::verify($this->node)->getBlockIndex(Phake::anyParameters());
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
            array('News', 1),
        );
    }
}
