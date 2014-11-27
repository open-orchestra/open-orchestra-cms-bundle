<?php

namespace PHPOrchestra\BackofficeBundle\Test\Command;

use Phake;
use PHPOrchestra\BackofficeBundle\Command\OrchestraCheckConsistencyCommand;
use PHPOrchestra\ModelBundle\Model\AreaInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * Class OrchestraCheckConsistencyCommandTest
 */
class OrchestraCheckConsistencyCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $kernel;
    protected $container;
    protected $nodeRepository;
    protected $trans;
    protected $node1;
    protected $node2;
    protected $areaMain;
    protected $areaMain2;
    protected $areaMenu;
    protected $areaFooter;
    protected $block1;
    protected $block2;
    protected $block3;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->node1 = Phake::mock('PHPOrchestra\ModelBundle\Document\Node');
        $this->node2 = Phake::mock('PHPOrchestra\ModelBundle\Document\Node');

        $this->block1 = Phake::mock('PHPOrchestra\ModelBundle\Document\Block');
        $this->block2 = Phake::mock('PHPOrchestra\ModelBundle\Document\Block');
        $this->block3 = Phake::mock('PHPOrchestra\ModelBundle\Document\Block');

        $this->areaMain = Phake::mock('PHPOrchestra\ModelBundle\Document\Area');
        $this->areaMain2 = Phake::mock('PHPOrchestra\ModelBundle\Document\Area');
        $this->areaMenu = Phake::mock('PHPOrchestra\ModelBundle\Document\Area');
        $this->areaFooter = Phake::mock('PHPOrchestra\ModelBundle\Document\Area');

        $this->trans = Phake::mock('Symfony\Component\Translation\Translator');
        Phake::when($this->trans)->trans('php_orchestra_backoffice.command.node.success')->thenReturn('success');
        Phake::when($this->trans)->trans('php_orchestra_backoffice.command.node.error')->thenReturn('error');
        Phake::when($this->trans)->trans('php_orchestra_backoffice.command.empty_choices')->thenReturn('empty');

        $this->nodeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');

        $this->container = $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        Phake::when($this->container)->get('php_orchestra_model.repository.node')->thenReturn($this->nodeRepository);
        Phake::when($this->container)->get('translator')->thenReturn($this->trans);

        $this->kernel = Phake::mock('Symfony\Component\HttpKernel\Kernel');
        Phake::when($this->kernel)->getContainer()->thenReturn($this->container);
    }

    /**
     * Test command OrchestraCheckConsistency
     */
    public function testExecute()
    {
        $application = new Application($this->kernel);
        $application->add(new OrchestraCheckConsistencyCommand());

        $command = $application->find('orchestra:check');
        $commandTest = new CommandTester($command);
        $commandTest->execute(array('command' => $command->getName()));

        $this->assertSame("empty\n", $commandTest->getDisplay());
    }

    /**
     * Test command OrchestraCheckConsistency with node option
     */
    public function testExecuteNode()
    {
        $nodes = $this->generateConsistencyNode();

        Phake::when($this->nodeRepository)->findAll()->thenReturn($nodes);

        Phake::when($this->node1)->getAreas()->thenReturn(array($this->areaMain));
        Phake::when($this->node1)->getNodeId()->thenReturn(NodeInterface::ROOT_NODE_ID);
        Phake::when($this->node1)->getBlocks()->thenReturn(array($this->block1, $this->block2, $this->block3));
        Phake::when($this->node1)->getBlock(0)->thenReturn($this->block1);
        Phake::when($this->node1)->getBlock(1)->thenReturn($this->block2);
        Phake::when($this->node1)->getBlock(2)->thenReturn($this->block3);

        Phake::when($this->node2)->getAreas()->thenReturn(array($this->areaMain2));
        Phake::when($this->node2)->getNodeId()->thenReturn('home');
        Phake::when($this->node2)->getBlocks()->thenReturn(array($this->block1, $this->block2));
        Phake::when($this->node2)->getBlock(0)->thenReturn($this->block1);
        Phake::when($this->node2)->getBlock(1)->thenReturn($this->block2);

        Phake::when($this->areaMain)->getAreaId()->thenReturn('main');
        Phake::when($this->areaMain)->getBlocks()->thenReturn(array(
            array('nodeId' => 0, 'blockId' => 0),
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'blockId' => 1),
            array('nodeId' => 0, 'blockId' => 2),
        ));

        Phake::when($this->areaMain2)->getAreaId()->thenReturn('main');
        Phake::when($this->areaMain2)->getBlocks()->thenReturn(array());
        Phake::when($this->areaMain2)->getAreas()->thenreturn(array($this->areaMenu, $this->areaFooter));

        Phake::when($this->areaMenu)->getAreaId()->thenReturn('menu');
        Phake::when($this->areaMenu)->getBlocks()->thenReturn(array(
            array('nodeId' => 0, 'blockId' => 0)
        ));

        Phake::when($this->areaFooter)->getAreaId()->thenReturn('footer');
        Phake::when($this->areaFooter)->getBlocks()->thenReturn(array(
            array('nodeId' => 0, 'blockId' => 1)
        ));

        Phake::when($this->block1)->getLabel()->thenReturn('menu');
        Phake::when($this->block1)->getAreas()->thenReturn(array(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'main'),
            array('nodeId' => 'home', 'areaId' => 'menu')
        ));

        Phake::when($this->block2)->getLabel()->thenReturn('footer');
        Phake::when($this->block2)->getAreas()->thenReturn(array(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'main'),
            array('nodeId' => 'home', 'areaId' => 'footer')
        ));

        Phake::when($this->block3)->getLabel()->thenReturn('header');
        Phake::when($this->block3)->getAreas()->thenReturn(array(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'main')
        ));

        $application = new Application($this->kernel);
        $application->add(new OrchestraCheckConsistencyCommand());

        $command = $application->find('orchestra:check');
        $commandTest = new CommandTester($command);
        $commandTest->execute(array('command' => $command->getName(), '--nodes' => true));

        $this->assertSame("success\n", $commandTest->getDisplay());
    }

    /**
     * @param NodeInterface $node
     * @param string        $nodeId
     *
     * @return NodeInterface
     */
    public function generateNode($node, $nodeId)
    {
        $node->setNodeId($nodeId);

        return $node;
    }
    /**
     * @param BlockInterface $block
     * @param string         $label
     * @param array          $areas
     *
     * @return BlockInterface
     */
    public function generateBlock($block, $label, $areas)
    {
        $block->setLabel($label);
        $block->setAreas($areas);

        return $block;
    }

    /**
     * @param AreaInterface $area
     * @param string        $areaId
     * @param array         $blocks
     *
     * @return AreaInterface
     */
    public function generateArea($area, $areaId, $blocks = array())
    {
        $area->setAreaId($areaId);
        $area->setBlocks($blocks);

        return $area;
    }

    /**
     * @return array
     */
    public function generateConsistencyNode()
    {
        $this->block1 = $this->generateBlock($this->block1, 'menu', array(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'main'),
            array('nodeId' => 'home', 'areaId' => 'menu')
        ));

        $this->block2 = $this->generateBlock($this->block2, 'footer', array(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'main'),
            array('nodeId' => 'home', 'areaId' => 'footer')
        ));

        $this->block3 = $this->generateBlock($this->block3, 'header', array(
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'areaId' => 'main')
        ));

        $this->areaMain = $this->generateArea($this->areaMain, 'main', array(
            array('nodeId' => 0, 'blockId' => 0),
            array('nodeId' => NodeInterface::ROOT_NODE_ID, 'blockId' => 1),
            array('nodeId' => 0, 'blockId' => 2),
        ));

        $this->node1 = $this->generateNode($this->node1, NodeInterface::ROOT_NODE_ID);
        $this->node1->addArea($this->areaMain);
        $this->node1->addBlock($this->block1);
        $this->node1->addBlock($this->block2);
        $this->node1->addBlock($this->block3);

        $this->areaMenu = $this->generateArea($this->areaMenu, 'menu', array(
            array('nodeId' => 0, 'blockId' => 0)
        ));

        $this->areaFooter = $this->generateArea($this->areaFooter, 'footer', array(
            array('nodeId' => 0, 'blockId' => 1)
        ));

        $this->areaMain2 = $this->generateArea($this->areaMain2, 'main');
        $this->areaMain2->addArea($this->areaMenu);
        $this->areaMain2->addArea($this->areaFooter);

        $this->node2 = $this->generateNode($this->node2, 'home');
        $this->node2->addArea($this->areaMain2);
        $this->node2->addBlock($this->block1);
        $this->node2->addBlock($this->block2);

        return array($this->node1, $this->node2);
    }
}
