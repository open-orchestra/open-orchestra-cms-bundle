<?php

namespace PHPOrchestra\ApiBundle\Test\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
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
    protected $areaTransformer;

    protected $currentNodeId = 'currentNodeId';
    protected $transformerManager;
    protected $areaId = 'areaId';
    protected $nodeRepository;
    protected $transformer;
    protected $otherNode;
    protected $block;
    protected $node;
    protected $area;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->area = Phake::mock('PHPOrchestra\ModelBundle\Model\AreaInterface');
        Phake::when($this->area)->getAreaId()->thenReturn($this->areaId);

        $this->block = Phake::mock('PHPOrchestra\ModelBundle\Model\BlockInterface');

        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($this->node)->getNodeId()->thenReturn($this->currentNodeId);
        Phake::when($this->node)->getBlock(Phake::anyParameters())->thenReturn($this->block);

        $this->otherNode = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($this->otherNode)->getBlock(Phake::anyParameters())->thenReturn($this->block);
        $this->nodeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');
        Phake::when($this->nodeRepository)->findOneByNodeIdAndSiteIdAndLastVersion(Phake::anyParameters())
            ->thenReturn($this->otherNode);

        $this->transformer = Phake::mock('PHPOrchestra\ApiBundle\Transformer\BlockTransformer');
        $this->transformerManager = Phake::mock('PHPOrchestra\ApiBundle\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->get(Phake::anyParameters())->thenReturn($this->transformer);

        $this->areaTransformer = new AreaTransformer($this->nodeRepository);

        $this->areaTransformer->setContext($this->transformerManager);
    }

    /**
     * @param string $nodeId
     * @param int    $blockId
     *
     * @dataProvider provideNodeAndBlockId
     */
    public function testReverseTransform($nodeId, $blockId)
    {
        $blockFacade = new BlockFacade();
        $blockFacade->nodeId = $nodeId;
        $blockFacade->blockId = $blockId;

        $facade = new AreaFacade();
        $facade->addBlock($blockFacade);

        Phake::when($this->transformer)->reverseTransformToArray(Phake::anyParameters())
            ->thenReturn(array('nodeId' => $nodeId, 'blockId' => $blockId));

        $this->areaTransformer->reverseTransform($facade, $this->area, $this->node);

        Phake::verify($this->transformer)->reverseTransformToArray($blockFacade, $this->node);
        Phake::verify($this->area)->setBlocks(array(
            0 => array('nodeId' => $nodeId, 'blockId' => $blockId)
        ));
        Phake::verify($this->nodeRepository)->findOneByNodeIdAndSiteIdAndLastVersion($nodeId);
        Phake::verify($this->block)->addArea(array('nodeId' => $this->currentNodeId, 'areaId' => $this->areaId));
    }

    /**
     * @return array
     */
    public function provideNodeAndBlockId()
    {
        return array(
            array('root', 1),
            array('root', 5),
            array('page_home', 3),
            array('fixture_full', 8),
        );
    }

    /**
     * @param string $nodeId
     * @param int    $blockId
     *
     * @dataProvider provideNodeAndBlockId
     */
    public function testReverseTransformWithCurrentNodeBlock($nodeId, $blockId)
    {
        $blockFacade = new BlockFacade();
        $blockFacade->nodeId = $nodeId;
        $blockFacade->blockId = $blockId;

        $facade = new AreaFacade();
        $facade->addBlock($blockFacade);

        Phake::when($this->transformer)->reverseTransformToArray(Phake::anyParameters())
            ->thenReturn(array('nodeId' => 0, 'blockId' => $blockId));

        $this->areaTransformer->reverseTransform($facade, $this->area, $this->node);

        Phake::verify($this->transformer)->reverseTransformToArray($blockFacade, $this->node);
        Phake::verify($this->area)->setBlocks(array(
            0 => array('nodeId' => 0, 'blockId' => $blockId)
        ));
        Phake::verify($this->block)->addArea(array('nodeId' => 0, 'areaId' => $this->areaId));
        Phake::verify($this->nodeRepository, Phake::never())->findOneByNodeIdAndSiteIdAndLastVersion(Phake::anyParameters());
    }
}
