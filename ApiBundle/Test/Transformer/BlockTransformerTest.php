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
    protected $translator;
    protected $blockTransformer;
    protected $blockFacade;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->displayBlockManager = Phake::mock('PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager');
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        $this->blockFacade = Phake::mock('PHPOrchestra\ApiBundle\Facade\BlockFacade');

        $this->blockTransformer = new BlockTransformer($this->displayBlockManager, $this->translator);
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
     * @param string      $component
     * @param array       $attributes
     * @param string      $label
     * @param string|null $nodeId
     * @param int|null    $blockNumber
     *
     * @dataProvider blockTransformProvider
     */
    public function testTransform($component, $attributes, $label, $nodeId = null, $blockNumber = null)
    {
        $html = 'ok';
        $block = Phake::mock('PHPOrchestra\ModelBundle\Model\BlockInterface');
        $response = Phake::mock('Symfony\Component\HttpFoundation\Response');
        $transformer = Phake::mock('PHPOrchestra\ApiBundle\Transformer\TransformerInterface');
        $transformerManager = Phake::mock('PHPOrchestra\ApiBundle\Transformer\TransformerManager');
        $facade = Phake::mock('PHPOrchestra\ApiBundle\Facade\UiModelFacade');

        Phake::when($block)->getComponent()->thenReturn($component);
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($label);
        Phake::when($block)->getAttributes()->thenReturn($attributes);
        Phake::when($this->displayBlockManager)->show($block)->thenReturn($response);
        Phake::when($response)->getContent()->thenReturn($html);

        $this->blockTransformer->setContext($transformerManager);

        Phake::when($transformerManager)->get('ui_model')->thenReturn($transformer);
        Phake::when($transformer)->transform(Phake::anyParameters())->thenReturn($facade);

        $facadeExcepted = $this->blockTransformer->transform($block, true);

        $this->assertInstanceOf('PHPOrchestra\ApiBundle\Facade\BlockFacade', $facadeExcepted);
        $this->assertSame($component, $facadeExcepted->component);
        $this->assertInstanceOf('PHPOrchestra\ApiBundle\Facade\UiModelFacade', $facadeExcepted->uiModel);
        $this->assertSame($attributes, $facadeExcepted->getAttributes());
    }

    /**
     * @return array
     */
    public function blockTransformProvider()
    {
        return array(
            array('sample', array('titre' => 'titre', 'author' => 'auteur'), 'Sample'),
            array(
                'sample',
                array('titre' => 'news', 'author' => 'benj', 'text' => 'salut a tous'),
                'Sample',
                'fixture_full',
                5
            ),
            array(
                'news',
                array('titre' => 'news', 'author' => 'benj', 'text' => 'salut a tous'),
                'News',
                'fixture_home',
                3
            ),
            array(
                'menu',
                array(),
                'Menu',
                2
            ),
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
    public function testReverseTransformToArray($nodeId,$result, $facadeNodeId, $blockId)
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
}
