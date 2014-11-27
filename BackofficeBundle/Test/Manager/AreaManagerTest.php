<?php

namespace PHPOrchestra\BackofficeBundle\Test\Manager;

use PHPOrchestra\ModelBundle\Model\AreaContainerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\BackofficeBundle\Manager\AreaManager;
use PHPOrchestra\ModelBundle\Document\Area;
use Phake;
use PHPOrchestra\ModelBundle\Model\NodeInterface;

/**
 * Class AreaManagerTest
 */
class AreaManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AreaManager
     */
    protected $manager;

    protected $language = 'fr';
    protected $nodeRepository;
    protected $block;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');

        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($this->node)->getLanguage()->thenReturn($this->language);

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
        Phake::verify($this->block, Phake::times(1))->removeAreaRef($areaId, $nodeId);
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
        Phake::when($this->nodeRepository)->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(Phake::anyParameters())->thenReturn($this->node);
        Phake::when($this->node)->getBlock(Phake::anyParameters())->thenReturn($this->block);
        Phake::when($this->node)->getNodeId()->thenReturn($nodeId);

        $this->manager->deleteAreaFromBlock($oldBlocks, $newBlocks, $areaId, $this->node);

        Phake::verify($this->nodeRepository, Phake::times(1))->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($nodeTransverseId, $this->language);
        Phake::verify($this->node, Phake::times(1))->getBlock(Phake::anyParameters());
        Phake::verify($this->block, Phake::times(1))->removeAreaRef($areaId, $nodeId);
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

    /**
     * @param NodeInterface $node
     *
     * @dataProvider provideNodeWithAreaAndBlock
     */
    public function testAreaConsistency($node)
    {
        $this->assertTrue($this->manager->areaConsistency($node));
    }

    /**
     * @return array
     */
    public function provideNodeWithAreaAndBlock()
    {
        $block1 = Phake::mock('PHPOrchestra\ModelBundle\Document\Block');
        Phake::when($block1)->getLabel()->thenReturn('menu');
        Phake::when($block1)->getAreas()->thenReturn(array(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'menu'),
            array('nodeId' => 0, 'areaId' => 'main')
        ));
        $block1->setLabel('menu');
        $block1->setAreas(array(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'menu'),
            array('nodeId' => 0, 'areaId' => 'main')
        ));

        $block2 = Phake::mock('PHPOrchestra\ModelBundle\Document\Block');
        Phake::when($block2)->getLabel()->thenReturn('footer');
        Phake::when($block2)->getAreas()->thenReturn(array(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'footer'),
            array('nodeId' => 0, 'areaId' => 'main')
        ));
        $block2->setLabel('footer');
        $block2->setAreas(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'footer'),
            array('nodeId' => 0, 'areaId' => 'main')
        );

        $block3 = Phake::mock('PHPOrchestra\ModelBundle\Document\Block');
        Phake::when($block3)->getLabel()->thenReturn('header');
        Phake::when($block3)->getAreas()->thenReturn(array(
            array('nodeId' => 0, 'areaId' => 'main')
        ));
        $block3->setLabel('header');
        $block3->setAreas(array(
            array('nodeId' => 0, 'areaId' => 'main')
        ));

        $areaMenu = Phake::mock('PHPOrchestra\ModelBundle\Document\Area');
        Phake::when($areaMenu)->getAreaId()->thenReturn('menu');
        Phake::when($areaMenu)->getBlocks()->thenReturn(array(array('nodeId' => 0, 'blockId' => 0)));
        $areaMenu->setAreaId('menu');
        $areaMenu->setBlocks(array(array('nodeId' => 0, 'blockId' => 0)));

        $areaFooter = Phake::mock('PHPOrchestra\ModelBundle\Document\Area');
        Phake::when($areaFooter)->getAreaId()->thenReturn('footer');
        Phake::when($areaFooter)->getBlocks()->thenReturn(array(array('nodeId' => 0, 'blockId' => 1)));
        $areaFooter->setAreaId('footer');
        $areaFooter->setBlocks(array(array('nodeId' => 0, 'blockId' => 1)));

        $areaMain = Phake::mock('PHPOrchestra\ModelBundle\Document\Area');
        Phake::when($areaMain)->getAreaId()->thenReturn('main');
        Phake::when($areaMain)->getBlocks()->thenReturn(array());
        Phake::when($areaMain)->getAreas()->thenReturn(array($areaMenu, $areaFooter));
        $areaMain->setAreaId('main');
        $areaMain->setBlocks(array());
        $areaMain->addArea($areaMenu);
        $areaMain->addArea($areaFooter);

        $areaMain2 = Phake::mock('PHPOrchestra\ModelBundle\Document\Area');
        Phake::when($areaMain2)->getAreaId()->thenReturn('main');
        Phake::when($areaMain2)->getBlocks()->thenReturn(array(
            array('nodeId' => 0, 'blockId' => 0),
            array('nodeId' => 'home', 'blockId' => 1),
            array('nodeId' => 0, 'blockId' => 2),
        ));
        $areaMain2->setAreaId('main');
        $areaMain2->setBlocks(array(
            array('nodeId' => 0, 'blockId' => 0),
            array('nodeId' => 'home', 'blockId' => 1),
            array('nodeId' => 0, 'blockId' => 2),
        ));

        $node = Phake::mock('PHPOrchestra\ModelBundle\Document\Node');
        Phake::when($node)->getAreas()->thenReturn(array($areaMain));
        Phake::when($node)->getNodeId()->thenReturn(NodeInterface::ROOT_NODE_ID);
        Phake::when($node)->getBlocks()->thenReturn(array($block1, $block2));
        Phake::when($node)->getBlock(0)->thenReturn($block1);
        Phake::when($node)->getBlock(1)->thenReturn($block2);
        $node->setNodeId(NodeInterface::ROOT_NODE_ID);
        $node->addArea($areaMain);
        $node->addBlock($block1);
        $node->addBlock($block2);

        $node2 = Phake::mock('PHPOrchestra\ModelBundle\Document\Node');
        Phake::when($node2)->getAreas()->thenReturn(array($areaMain2));
        Phake::when($node2)->getNodeId()->thenReturn('home');
        Phake::when($node2)->getBlocks()->thenReturn(array($block1, $block2, $block3));
        Phake::when($node2)->getBlock(0)->thenReturn($block1);
        Phake::when($node2)->getBlock(1)->thenReturn($block2);
        Phake::when($node2)->getBlock(2)->thenReturn($block3);
        $node2->setNodeId('home');
        $node2->addArea($areaMain2);
        $node2->addBlock($block1);
        $node2->addBlock($block2);
        $node2->addBlock($block3);

        return array(
            array($node),
            array($node2)
        );
    }
}
