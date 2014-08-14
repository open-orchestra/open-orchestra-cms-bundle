<?php

namespace PHPOrchestra\ApiBundle\Test\Transformer;

use Phake;
use PHPOrchestra\ApiBundle\Facade\AreaFacade;
use PHPOrchestra\ApiBundle\Facade\BlockFacade;
use PHPOrchestra\ApiBundle\Transformer\AreaTransformer;

/**
 * Class AreaTransformerTest
 */
class AreaTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AreaTransformer
     */
    protected $transformer;

    protected $context;
    protected $repository;
    protected $abstractTransformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->abstractTransformer = Phake::mock('PHPOrchestra\ApiBundle\Transformer\BlockTransformer');
        $this->context = Phake::mock('PHPOrchestra\ApiBundle\Transformer\TransformerManager');
        Phake::when($this->context)->get(Phake::anyParameters())->thenReturn($this->abstractTransformer);

        $this->repository =Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');

        $this->transformer = new AreaTransformer($this->repository);
        $this->transformer->setContext($this->context);
    }

    /**
     * @param string $areaId
     * @param array  $classes
     * @param string $nodeId
     * @param int    $blockId
     *
     * @dataProvider provideReverseTransformData
     */
    public function testReverseTransformWithOnlyLoad($areaId, $classes, $nodeId, $blockId)
    {
        $block = new BlockFacade();
        $block->nodeId = $nodeId;
        $block->blockId = $blockId;
        $block->method = BlockFacade::LOAD;

        $subArea = new AreaFacade();

        $facade = new AreaFacade();
        $facade->areaId = $areaId;
        $facade->classes = implode(',', $classes);
        $facade->addArea($subArea);
        $facade->addArea($subArea);
        $facade->addBlock($block);
        $facade->addBlock($block);

        $area = Phake::mock('PHPOrchestra\ModelBundle\Document\Area');
        $subAreaDocument = Phake::mock('PHPOrchestra\ModelBundle\Model\AreaInterface');

        Phake::when($this->abstractTransformer)->reverseTransform($block, Phake::anyParameters())->thenReturn(array(
            'nodeId' => $nodeId,
            'blockId' => $blockId
        ));

        Phake::when($this->abstractTransformer)->reverseTransform($subArea, null, null)->thenReturn($subAreaDocument);

        $returnedArea = $this->transformer->reverseTransform($facade, $area);

        $this->assertSame($area, $returnedArea);
        Phake::verify($area)->setAreaId($areaId);
        Phake::verify($area)->setClasses($classes);
        Phake::verify($this->context, Phake::times(2))->get('block');
        Phake::verify($this->context, Phake::times(2))->get('area');
        Phake::verify($this->abstractTransformer, Phake::times(2))->reverseTransform($block, Phake::anyParameters());
        Phake::verify($this->abstractTransformer, Phake::times(2))->reverseTransform($subArea, null, null);
        Phake::verify($area, Phake::times(2))->addBlock(array('nodeId' => $nodeId, 'blockId' => $blockId));
        Phake::verify($area, Phake::times(2))->addSubArea($subAreaDocument);
    }

    /**
     * @return array
     */
    public function provideReverseTransformData()
    {
        return array(
            array('main', array('test'), 'first', 0),
            array('other', array('template'), 'last', 0),
            array('last', array('footer'), 'header', 0),
        );
    }

    /**
     * @param string $areaId
     * @param array  $classes
     * @param string $nodeId
     * @param int    $blockId
     *
     * @dataProvider provideReverseTransformData
     */
    public function testReverseTransformWithOnlyGenerate($areaId, $classes, $nodeId, $blockId)
    {
        $block = new BlockFacade();
        $block->nodeId = $nodeId;
        $block->blockId = $blockId;
        $block->method = BlockFacade::GENERATE;

        $subArea = new AreaFacade();

        $facade = new AreaFacade();
        $facade->areaId = $areaId;
        $facade->classes = implode(',',$classes);
        $facade->addArea($subArea);
        $facade->addArea($subArea);
        $facade->addBlock($block);
        $facade->addBlock($block);

        $area = Phake::mock('PHPOrchestra\ModelBundle\Document\Area');
        $subAreaDocument = Phake::mock('PHPOrchestra\ModelBundle\Model\AreaInterface');
        $blockDocument = Phake::mock('PHPOrchestra\ModelBundle\Model\BlockInterface');
        $node = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');

        Phake::when($this->abstractTransformer)->reverseTransform(Phake::anyParameters())->thenReturn(array(
            'nodeId' => $nodeId,
            'blockId' => $blockId,
            'block' => $blockDocument
        ));

        Phake::when($this->abstractTransformer)->reverseTransform($subArea, null, $node)->thenReturn($subAreaDocument);

        $returnedArea = $this->transformer->reverseTransform($facade, $area, $node);

        $this->assertSame($area, $returnedArea);
        Phake::verify($area)->setAreaId($areaId);
        Phake::verify($area)->setClasses($classes);
        Phake::verify($this->context, Phake::times(2))->get('block');
        Phake::verify($this->context, Phake::times(2))->get('area');
        Phake::verify($this->abstractTransformer, Phake::times(2))->reverseTransform($block, $node);
        Phake::verify($this->abstractTransformer, Phake::times(2))->reverseTransform($subArea, null, $node);
        Phake::verify($area, Phake::times(2))->addBlock(array('nodeId' => $nodeId, 'blockId' => $blockId));
        Phake::verify($node, Phake::times(2))->setBlock($blockId, $blockDocument);
        Phake::verify($area, Phake::times(2))->addSubArea($subAreaDocument);
    }
}
