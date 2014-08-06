<?php

namespace PHPOrchestra\ApiBundle\Test\Transformer;

use Phake;
use PHPOrchestra\ApiBundle\Facade\BlockFacade;
use PHPOrchestra\ApiBundle\Transformer\BlockTransformer;
use PHPOrchestra\ModelBundle\Document\Node;

/**
 * Class BlockTransformerTest
 */
class BlockTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockTransformer
     */
    protected $transformer;

    protected $node;
    protected $displayBlockManager;

    public function setUp()
    {
        $this->displayBlockManager = Phake::mock('PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager');
        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');

        $this->transformer = new BlockTransformer($this->displayBlockManager);
    }

    /**
     * @param string $nodeId
     * @param int    $blockId
     * @param string $component
     *
     * @dataProvider provideBlockInfo
     */
    public function testReverseTransformWithLoad($nodeId, $blockId, $component)
    {
        $block = new BlockFacade();
        $block->nodeId = $nodeId;
        $block->blockId = $blockId;
        $block->method = BlockFacade::LOAD;
        $block->addAttribute('testKey', 'testValue');

        $blockReturned = $this->transformer->reverseTransform($block, $this->node);

        $this->assertSame(array('nodeId' => $nodeId, 'blockId' => $blockId), $blockReturned);
        Phake::verify($this->node, Phake::never())->addBlock(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideBlockInfo()
    {
        return array(
            array('node', 1, 'Sample'),
            array('main', 3, 'Search'),
            array('template', 5, 'Other'),
        );
    }

    /**
     * @param string $nodeId
     * @param int    $blockId
     * @param string $component
     *
     * @dataProvider provideBlockInfo
     */
    public function testReverseTransformWithGenerateAndExistingBlock($nodeId, $blockId, $component)
    {
        $block = new BlockFacade();
        $block->component = $component;
        $block->blockId = $blockId;
        $block->method = BlockFacade::GENERATE;
        $block->addAttribute('testKey', 'testValue');

        $blockDocument = Phake::mock('PHPOrchestra\ModelBundle\Model\BlockInterface');
        $node = new Node();
        $node->setBlock($blockId, $blockDocument);

        $blockReturned = $this->transformer->reverseTransform($block, $node);

        $this->assertSame(array('nodeId' => 0, 'blockId' => $blockId, 'block' => $blockDocument), $blockReturned);
        Phake::verify($blockDocument)->setComponent($component);
        Phake::verify($blockDocument)->setAttributes(array('testKey' => 'testValue'));
    }

    /**
     * @param string $nodeId
     * @param int    $blockId
     * @param string $component
     *
     * @dataProvider provideBlockInfo
     */
    public function testReverseTransformWithGenerateAndNoExistingBlock($nodeId, $blockId, $component)
    {
        $block = new BlockFacade();
        $block->component = $component;
        $block->method = BlockFacade::GENERATE;
        $block->addAttribute('testKey', 'testValue');

        $blockDocument = Phake::mock('PHPOrchestra\ModelBundle\Model\BlockInterface');
        $node = new Node();
        $node->setBlock(0, $blockDocument);

        $blockReturned = $this->transformer->reverseTransform($block, $node);

        $this->assertSame(0, $blockReturned['nodeId']);
        $this->assertSame(1, $blockReturned['blockId']);
        $this->assertInstanceOf('PHPOrchestra\ModelBundle\Model\BlockInterface', $blockReturned['block']);
        $this->assertSame($component, $blockReturned['block']->getComponent());
        $this->assertSame(array('testKey' => 'testValue'), $blockReturned['block']->getAttributes());
    }
}
