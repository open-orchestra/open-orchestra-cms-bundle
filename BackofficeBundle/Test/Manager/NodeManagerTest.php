<?php

namespace PHPOrchestra\BackofficeBundle\Test\Manager;

use PHPOrchestra\BackofficeBundle\Manager\NodeManager;
use PHPOrchestra\ModelBundle\Document\Area;
use PHPOrchestra\ModelBundle\Document\Block;
use PHPOrchestra\ModelBundle\Document\Node;
use PHPOrchestra\ModelInterface\Model\NodeInterface;
use PHPOrchestra\ModelBundle\Repository\NodeRepository;
use Phake;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class NodeManagerTest
 */
class NodeManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeManager
     */
    protected $manager;

    protected $nodeRepository;
    protected $siteRepository;
    protected $areaManager;
    protected $blockManager;
    protected $contextManager;
    protected $nodeClass;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $theme = Phake::mock('PHPOrchestra\ModelInterface\Model\ThemeInterface');
        Phake::when($theme)->getName()->thenReturn('fakeNameTheme');
        $site = Phake::mock('PHPOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getTheme()->thenReturn($theme);

        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Document\Node');
        $this->nodeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');
        $this->siteRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\SiteRepository');
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($site);
        $this->areaManager = Phake::mock('PHPOrchestra\BackofficeBundle\Manager\AreaManager');
        $this->blockManager = Phake::mock('PHPOrchestra\BackofficeBundle\Manager\BlockManager');
        $this->contextManager = Phake::mock('PHPOrchestra\Backoffice\Context\ContextManager');
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn('fakeSiteId');
        Phake::when($this->contextManager)->getCurrentSiteDefaultLanguage()->thenReturn('fakeLanguage');
        $this->nodeClass = 'PHPOrchestra\ModelBundle\Document\Node';

        $this->manager = new NodeManager($this->nodeRepository, $this->siteRepository, $this->areaManager, $this->blockManager, $this->contextManager, $this->nodeClass);
    }

    /**
     * @param NodeInterface   $node
     * @param int             $expectedVersion
     *
     * @dataProvider provideNode
     */
    public function testDuplicateNode(NodeInterface $node, $expectedVersion)
    {
        $alteredNode = $this->manager->duplicateNode($node);

        Phake::verify($alteredNode)->setVersion($expectedVersion);
        Phake::verify($alteredNode)->setStatus(null);
    }

    /**
     * @return array
     */
    public function provideNode()
    {
        $node0 = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node0)->getVersion()->thenReturn(0);
        Phake::when($node0)->getAreas()->thenReturn(new ArrayCollection());
        Phake::when($node0)->getBlocks()->thenReturn(new ArrayCollection());

        $node1 = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node1)->getVersion()->thenReturn(1);
        Phake::when($node1)->getAreas()->thenReturn(new ArrayCollection());
        Phake::when($node1)->getBlocks()->thenReturn(new ArrayCollection());

        $node2 = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node2)->getVersion()->thenReturn(null);
        Phake::when($node2)->getAreas()->thenReturn(new ArrayCollection());
        Phake::when($node2)->getBlocks()->thenReturn(new ArrayCollection());

        return array(
            array($node0, 1),
            array($node1, 2),
            array($node2, 1),
        );
    }

    /**
     * @param NodeInterface   $node
     * @param string          $language
     *
     * @dataProvider provideNodeAndLanguage
     */
    public function testCreateNewLanguageNode(NodeInterface $node, $language)
    {
        $alteredNode = $this->manager->createNewLanguageNode($node, $language);

        Phake::verify($alteredNode)->setVersion(1);
        Phake::verify($alteredNode)->setStatus(null);
        Phake::verify($alteredNode)->setLanguage($language);
    }

    /**
     * @return array
     */
    public function provideNodeAndLanguage()
    {
        $node0 = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node0)->getVersion()->thenReturn(0);
        Phake::when($node0)->getAreas()->thenReturn(new ArrayCollection());
        Phake::when($node0)->getBlocks()->thenReturn(new ArrayCollection());

        $node1 = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node1)->getVersion()->thenReturn(1);
        Phake::when($node1)->getAreas()->thenReturn(new ArrayCollection());
        Phake::when($node1)->getBlocks()->thenReturn(new ArrayCollection());

        return array(
            array($node0, 'fr'),
            array($node1, 'en'),
        );
    }

    /**
     * Test deleteTree
     */
    public function testDeleteTree()
    {
        $nodeId = 'nodeId';
        $node = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getNodeId()->thenReturn($nodeId);
        $nodes = new ArrayCollection();
        $nodes->add($node);
        $nodes->add($node);

        $sonId = 'sonId';
        $son = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($son)->getNodeId()->thenReturn($sonId);
        $sons = new ArrayCollection();
        $sons->add($son);
        $sons->add($son);

        Phake::when($this->nodeRepository)->findByParentIdAndSiteId($nodeId)->thenReturn($sons);
        Phake::when($this->nodeRepository)->findByParentIdAndSiteId($sonId)->thenReturn(new ArrayCollection());

        $this->manager->deleteTree($nodes);

        Phake::verify($node, Phake::times(2))->setDeleted(true);
        Phake::verify($son, Phake::times(2))->setDeleted(true);
        Phake::verify($this->nodeRepository)->findByParentIdAndSiteId($nodeId);
        Phake::verify($this->nodeRepository)->findByParentIdAndSiteId($sonId);
    }

    /**
     * test hydrateNodeFromNodeId
     */
    public function testHydrateNodeFromNodeId()
    {
        $newNode = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');

        $area = new Area();
        $areas = new ArrayCollection();
        $areas->add($area);
        $block = new Block();
        $blocks = new ArrayCollection();
        $blocks->add($block);
        $oldNodeId = 'oldNodeId';
        $oldNode = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($oldNode)->getAreas()->thenReturn($areas);
        Phake::when($oldNode)->getBlocks()->thenReturn($blocks);
        Phake::when($this->nodeRepository)->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(Phake::anyParameters())->thenReturn($oldNode);

        $this->manager->hydrateNodeFromNodeId($newNode, $oldNodeId);

        Phake::verify($newNode)->addBlock($block);
        Phake::verify($newNode)->addArea($area);
    }

    /**
     * Test nodeConsistency
     *
     * @param array $nodes
     *
     * @dataProvider generateConsistencyNode
     */
    public function testNodeConsistency($nodes)
    {
        Phake::when($this->areaManager)->areaConsistency(Phake::anyParameters())->thenReturn(true);
        Phake::when($this->blockManager)->blockConsistency(Phake::anyParameters())->thenReturn(true);

        $this->assertTrue($this->manager->nodeConsistency($nodes));
    }

    /**
     * @return array
     */
    public function generateConsistencyNode()
    {
        $areaContainer = Phake::mock('PHPOrchestra\ModelInterface\Model\AreaContainerInterface');

        return array(
            array(array($areaContainer, $areaContainer, $areaContainer)),
            array(array()),
        );
    }

    /**
     * Test initializeNewNode
     */
    public function testInitializeNewNode()
    {
        $node = $this->manager->initializeNewNode();

        $this->assertInstanceOf($this->nodeClass, $node);
        $this->assertEquals('fakeSiteId', $node->getSiteId());
        $this->assertEquals('fakeLanguage', $node->getLanguage());
        $this->assertEquals('fakeNameTheme', $node->getTheme());
    }

    /**
     * @param bool $areaConsistency
     * @param bool $blockConsistency
     *
     * @dataProvider provideConsistency
     */
    public function testNodeNoConsistency($areaConsistency, $blockConsistency)
    {
        $areaContainer = Phake::mock('PHPOrchestra\ModelInterface\Model\AreaContainerInterface');
        Phake::when($this->areaManager)->areaConsistency(Phake::anyParameters())->thenReturn($areaConsistency);
        Phake::when($this->blockManager)->blockConsistency(Phake::anyParameters())->thenReturn($blockConsistency);

        $this->assertFalse($this->manager->nodeConsistency(array($areaContainer)));
    }

    /**
     * @return array
     */
    public function provideConsistency()
    {
        return array(
            array(true, false),
            array(false, false),
            array(false, true),
        );
    }

    /**
     * @param string $oldId
     * @param string $newId
     *
     * @dataProvider provideNodesAndReferences
     */
    public function testUpdateBlockReferences($oldId, $newId)
    {
        $block1 = Phake::mock('PHPOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block1)->getAreas()
            ->thenReturn(array(array('nodeId' => 0, 'areaId' => 'main'), array('nodeId' => $oldId, 'areaId' => 'main')));

        $block2 = Phake::mock('PHPOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block2)->getAreas()->thenReturn(array(array('nodeId' => $oldId, 'areaId' => 'main')));

        $block3 = Phake::mock('PHPOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block3)->getAreas()->thenReturn(array(array('nodeId' => 0, 'areaId' => 'main')));

        $area1 = Phake::mock('PHPOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($area1)->getBlocks()
            ->thenReturn(array(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0)));
        Phake::when($area1)->getAreaId()->thenReturn('main');

        $area2 = Phake::mock('PHPOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($area2)->getBlocks()
            ->thenReturn(array(array('nodeId' => 'oldNode', 'blockId' => 0), array('nodeId' => 0, 'blockId' => 1)));

        $transverseNode = Phake::mock('PHPOrchestra\ModelBundle\Document\Node');
        Phake::when($transverseNode)->getBlock(0)->thenReturn($block1);

        $newNode = Phake::mock('PHPOrchestra\ModelBundle\Document\Node');
        Phake::when($newNode)->getId()->thenReturn($newId);
        Phake::when($newNode)->getBlock(0)->thenReturn($block2);
        Phake::when($newNode)->getBlock(1)->thenReturn($block3);
        Phake::when($newNode)->getAreas()->thenReturn(new ArrayCollection(array($area1, $area2)));

        $oldNode = Phake::mock('PHPOrchestra\ModelBundle\Document\Node');
        Phake::when($oldNode)->getId()->thenReturn($oldId);

        Phake::when($this->nodeRepository)
            ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(Phake::anyParameters())->thenReturn($transverseNode);

        $this->manager->updateBlockReferences($oldNode, $newNode);

        Phake::verify($this->nodeRepository)->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(Phake::anyParameters());
        Phake::verify($block1)->addArea(array('nodeId' => $newId, 'areaId' => 'main'));
    }

    /**
     * @return array
     */
    public function provideNodesAndReferences()
    {
        return array(
            array('oldNode', 'newNode'),
            array('vieux', 'jeune')
        );
    }
}
