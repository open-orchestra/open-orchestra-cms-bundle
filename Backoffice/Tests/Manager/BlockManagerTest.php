<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Manager\BlockManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class BlockManagerTest
 */
class BlockManagerTest extends AbstractBaseTestCase
{
    protected $manager;
    protected $nodeRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');

        $this->manager = new BlockManager($this->nodeRepository);
    }

    /**
     * @param NodeInterface $node
     * @param NodeInterface $node2
     *
     * @dataProvider provideNodeWithAreaAndBlock
     */
    public function testBlockConsistency($node, $node2)
    {
        Phake::when($this->nodeRepository)
            ->findInLastVersion(Phake::anyParameters())->thenReturn($node2);
        Phake::when($this->nodeRepository)->find(Phake::anyParameters())->thenReturn($node2);

        $this->assertTrue($this->manager->blockConsistency($node));
    }

    /**
     * @return array
     */
    public function provideNodeWithAreaAndBlock()
    {
        $block1 = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block1)->getLabel()->thenReturn('menu');
        Phake::when($block1)->getAreas()->thenReturn(array(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'menu'),
            array('nodeId' => 'home', 'areaId' => 'main')
        ));
        $block1->setLabel('menu');
        $block1->setAreas(array(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'menu'),
            array('nodeId' => 'home', 'areaId' => 'main')
        ));

        $block2 = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block2)->getLabel()->thenReturn('footer');
        Phake::when($block2)->getAreas()->thenReturn(array(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'footer'),
            array('nodeId' => 'home', 'areaId' => 'main')
        ));
        $block2->setLabel('footer');
        $block2->setAreas(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'footer'),
            array('nodeId' => 'home', 'areaId' => 'main')
        );

        $block3 = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block3)->getLabel()->thenReturn('header');
        Phake::when($block3)->getAreas()->thenReturn(array(
            array('nodeId' => 'home', 'areaId' => 'main')
        ));
        $block3->setLabel('header');
        $block3->setAreas(array(
            array('nodeId' => 'home', 'areaId' => 'main')
        ));

        $areaMenu = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($areaMenu)->getAreaId()->thenReturn('menu');
        Phake::when($areaMenu)->getBlocks()->thenReturn(array(array('nodeId' => 0, 'blockId' => 0)));
        $areaMenu->setAreaId('menu');
        $areaMenu->setBlocks(array(array('nodeId' => 0, 'blockId' => 0)));

        $areaFooter = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($areaFooter)->getAreaId()->thenReturn('footer');
        Phake::when($areaFooter)->getBlocks()->thenReturn(array(array('nodeId' => 0, 'blockId' => 1)));
        $areaFooter->setAreaId('footer');
        $areaFooter->setBlocks(array(array('nodeId' => 0, 'blockId' => 1)));

        $areaMain = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($areaMain)->getAreaId()->thenReturn('main');
        Phake::when($areaMain)->getBlocks()->thenReturn(array());
        Phake::when($areaMain)->getAreas()->thenReturn(array($areaMenu, $areaFooter));
        $areaMain->setAreaId('main');
        $areaMain->setBlocks(array());
        $areaMain->addArea($areaMenu);
        $areaMain->addArea($areaFooter);

        $areaMain2 = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
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

        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getAreas()->thenReturn(array($areaMain));
        Phake::when($node)->getNodeId()->thenReturn(NodeInterface::ROOT_NODE_ID);
        Phake::when($node)->getId()->thenReturn(NodeInterface::ROOT_NODE_ID);
        Phake::when($node)->getBlocks()->thenReturn(array($block1, $block2));
        Phake::when($node)->getBlock(0)->thenReturn($block1);
        Phake::when($node)->getBlock(1)->thenReturn($block2);
        $node->setNodeId(NodeInterface::ROOT_NODE_ID);
        $node->addArea($areaMain);
        $node->addBlock($block1);
        $node->addBlock($block2);

        $node2 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node2)->getAreas()->thenReturn(array($areaMain2));
        Phake::when($node2)->getId()->thenReturn('home');
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
            array($node, $node2),
            array($node2, $node)
        );
    }
}
