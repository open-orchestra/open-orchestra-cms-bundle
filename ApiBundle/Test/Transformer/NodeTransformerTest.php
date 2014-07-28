<?php

namespace PHPOrchestra\ApiBundle\Test\Transformer;

use Phake;
use PHPOrchestra\ApiBundle\Facade\AreaFacade;
use PHPOrchestra\ApiBundle\Facade\BlockFacade;
use PHPOrchestra\ApiBundle\Facade\NodeFacade;
use PHPOrchestra\ApiBundle\Transformer\NodeTransformer;

/**
 * Class NodeTransformerTest
 */
class NodeTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeTransformer
     */
    protected $transformer;

    protected $context;
    protected $abstractTransformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->abstractTransformer = Phake::mock('PHPOrchestra\ApiBundle\Transformer\TransformerInterface');
        $this->context = Phake::mock('PHPOrchestra\ApiBundle\Transformer\TransformerManager');
        Phake::when($this->context)->get(Phake::anyParameters())->thenReturn($this->abstractTransformer);

        $this->transformer = new NodeTransformer();
        $this->transformer->setContext($this->context);
    }

    /**
     * @param $nodeId
     * @param $blockId
     *
     * @dataProvider provideNodeAndBlockId
     */
    public function testReverseTransform($nodeId, $blockId)
    {
        $areaFacade = new AreaFacade();
        $blockFacade = new BlockFacade();
        $nodeFacade = new NodeFacade();
        $nodeFacade->addArea($areaFacade);
        $nodeFacade->addArea($areaFacade);
        $nodeFacade->addBlock($blockFacade);
        $nodeFacade->addBlock($blockFacade);

        $node = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        $block = Phake::mock('PHPOrchestra\ModelBundle\Model\BlockInterface');
        $area = Phake::mock('PHPOrchestra\ModelBundle\Model\AreaInterface');

        Phake::when($this->abstractTransformer)->reverseTransform($areaFacade, null, $node)->thenReturn($area);
        Phake::when($this->abstractTransformer)->reverseTransform($blockFacade, $node)->thenReturn(array(
            'blockId' => $blockId,
            'nodeId' => $nodeId,
            'block' => $block
        ));

        $nodeReturned = $this->transformer->reverseTransform($nodeFacade, $node);

        $this->assertSame($nodeReturned, $node);
        Phake::verify($node, Phake::times(2))->addArea($area);
        Phake::verify($node, Phake::times(2))->setBlock($blockId, $block);
    }

    /**
     * @return array
     */
    public function provideNodeAndBlockId()
    {
        return array(
            array('test', 1),
            array('node', 2),
            array('template', 3),
        );
    }
}
 