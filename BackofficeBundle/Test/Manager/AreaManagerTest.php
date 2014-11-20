<?php

namespace PHPOrchestra\BackofficeBundle\Test\Manager;

use PHPOrchestra\ModelBundle\Model\AreaContainerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\BackofficeBundle\Manager\AreaManager;
use PHPOrchestra\ModelBundle\Document\Area;
use Phake;

/**
 * Class AreaManagerTest
 */
class AreaManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AreaManager
     */
    protected $manager;
    protected $nodeRepository;
    protected $node;
    protected $block;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');
        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        $this->block = Phake::mock('PHPOrchestra\ModelBundle\Model\BlockInterface');

        $this->manager = new AreaManager($this->nodeRepository);
    }

    /**
     * @param AreaContainerInterface $areaContainer
     * @param string                 $areaId
     * @param AreaContainerInterface $expectedArea
     *
     * @dataProvider provideAreaAndAreaId
     */
    public function testDeleteAreaFromAreas(AreaContainerInterface $areaContainer, $areaId, AreaContainerInterface $expectedArea)
    {
        $this->manager->deleteAreaFromAreas($areaContainer, $areaId);

        $this->assertTrue(
            $this->arrayContains($expectedArea->getAreas(), $areaContainer->getAreas())
            && $this->arrayContains($areaContainer->getAreas(), $expectedArea->getAreas())
        );
    }

    /**
     * @param ArrayCollection $refArray
     * @param ArrayCollection $includedArray
     *
     * @return bool
     */
    protected function arrayContains(ArrayCollection $refArray, ArrayCollection $includedArray)
    {
        if (count($includedArray) > 0) {
            foreach($includedArray as $element) {
                if (!$refArray->contains($element)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function provideAreaAndAreaId()
    {
        $area1 = Phake::mock('PHPOrchestra\ModelBundle\Model\AreaInterface');
        Phake::when($area1)->getAreaId()->thenReturn('area1');

        $area2 = Phake::mock('PHPOrchestra\ModelBundle\Model\AreaInterface');
        Phake::when($area2)->getAreaId()->thenReturn('area2');

        $area3 = Phake::mock('PHPOrchestra\ModelBundle\Model\AreaInterface');
        Phake::when($area3)->getAreaId()->thenReturn('area3');

        $emptyArea = new Area();

        $area = new Area();
        $area->addArea($area1); $area->addArea($area2); $area->addArea($area3);

        $filteredArea = new Area();
        $filteredArea->addArea($area1); $filteredArea->addArea($area3);

        return array(
            array($emptyArea, 'miscId', $emptyArea),
            array($area, 'miscId', $area),
            array($area, 'area2', $filteredArea)
        );
    }

    /**
     * @param array  $oldBlocks
     * @param array  $newBlocks
     * @param string $areaId
     * @param string $nodeId
     *
     * @dataProvider provideBlocks
     */
    public function testDeleteAreaFromBlock($oldBlocks, $newBlocks, $areaId, $nodeId)
    {
        Phake::when($this->node)->getBlock(Phake::anyParameters())->thenReturn($this->block);
        Phake::when($this->node)->getNodeId()->thenReturn($nodeId);

        $this->manager->deleteAreaFromBlock($oldBlocks, $newBlocks, $areaId, $this->node);

        Phake::verify($this->node, Phake::times(1))->getBlock(Phake::anyParameters());
        Phake::verify($this->block, Phake::times(1))->removeAreaByAreaIdAndNodeId($areaId, $nodeId);
        Phake::verify($this->node, Phake::times(1))->getNodeId();
    }

    /**
     * @return array
     */
    public function provideBlocks()
    {
        return array(
            array(
                array(
                    array('nodeId' => 0, 'blockId' => 0),
                    array('nodeId' => 0, 'blockId' => 1),
                    array('nodeId' => 0, 'blockId' => 2),
                    array('nodeId' => 0, 'blockId' => 3),
                ),
                array(
                    array('nodeId' => 0, 'blockId' => 0),
                    array('nodeId' => 0, 'blockId' => 1),
                    array('nodeId' => 0, 'blockId' => 3),
                ),
                'test2',
                'node-test'
            ),
        );
    }

    /**
     * @param array  $oldBlocks
     * @param array  $newBlocks
     * @param string $areaId
     * @param string $nodeId
     * @param string $nodeTransverseId
     *
     * @dataProvider provideBlocksWithNodeId
     */
    public function testDeleteAreaFromBlockWithNodeId($oldBlocks, $newBlocks, $areaId, $nodeId, $nodeTransverseId)
    {
        Phake::when($this->nodeRepository)->findOneByNodeIdAndSiteIdAndLastVersion(Phake::anyParameters())->thenReturn($this->node);
        Phake::when($this->node)->getBlock(Phake::anyParameters())->thenReturn($this->block);
        Phake::when($this->node)->getNodeId()->thenReturn($nodeId);

        $this->manager->deleteAreaFromBlock($oldBlocks, $newBlocks, $areaId, $this->node);

        Phake::verify($this->nodeRepository, Phake::times(1))->findOneByNodeIdAndSiteIdAndLastVersion($nodeTransverseId);
        Phake::verify($this->node, Phake::times(1))->getBlock(Phake::anyParameters());
        Phake::verify($this->block, Phake::times(1))->removeAreaByAreaIdAndNodeId($areaId, $nodeId);
        Phake::verify($this->node, Phake::times(1))->getNodeId();
    }

    /**
     * @return array
     */
    public function provideBlocksWithNodeId()
    {
        return array(
            array(
                array(
                    array('nodeId' => 'root', 'blockId' => 0),
                    array('nodeId' => 'root', 'blockId' => 1),
                    array('nodeId' => 'root', 'blockId' => 2),
                    array('nodeId' => 'root', 'blockId' => 3),
                ),
                array(
                    array('nodeId' => 'root', 'blockId' => 0),
                    array('nodeId' => 'root', 'blockId' => 1),
                    array('nodeId' => 'root', 'blockId' => 3),
                ),
                'test2',
                'node-test',
                'root'
            ),
        );
    }
}
