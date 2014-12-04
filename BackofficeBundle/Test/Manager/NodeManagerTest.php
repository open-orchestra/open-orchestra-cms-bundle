<?php

namespace PHPOrchestra\BackofficeBundle\Test\Manager;

use PHPOrchestra\BackofficeBundle\Manager\NodeManager;
use PHPOrchestra\ModelBundle\Document\Area;
use PHPOrchestra\ModelBundle\Document\Block;
use PHPOrchestra\ModelBundle\Document\Node;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
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
        $theme = Phake::mock('PHPOrchestra\ModelBundle\Model\ThemeInterface');
        Phake::when($theme)->getName()->thenReturn('fakeNameTheme');
        $site = Phake::mock('PHPOrchestra\ModelBundle\Model\SiteInterface');
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
     * @param NodeRepository $nodeRepository
     * @param NodeInterface  $nodeToDelete
     * @param array          $nodes
     *
     * @dataProvider provideNodeToDelete
     */
    public function testDeleteTree(NodeRepository $nodeRepository, NodeInterface $nodeToDelete, $nodes)
    {
        $manager = new NodeManager($nodeRepository, $this->siteRepository, $this->areaManager, $this->blockManager, $this->contextManager, $this->nodeClass);

        $manager->deleteTree($nodeToDelete);

        foreach ($nodes as $node) {
            Phake::verify($node, Phake::times(1))->setDeleted(true);
        }
    }

    /**
     * @return array
     */
    public function provideNode()
    {
        $node0 = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($node0)->getVersion()->thenReturn(0);
        Phake::when($node0)->getAreas()->thenReturn(new ArrayCollection());
        Phake::when($node0)->getBlocks()->thenReturn(new ArrayCollection());

        $node1 = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($node1)->getVersion()->thenReturn(1);
        Phake::when($node1)->getAreas()->thenReturn(new ArrayCollection());
        Phake::when($node1)->getBlocks()->thenReturn(new ArrayCollection());

        $node2 = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
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
     * @return array
     */
    public function provideNodeAndLanguage()
    {
        $node0 = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($node0)->getVersion()->thenReturn(0);
        Phake::when($node0)->getAreas()->thenReturn(new ArrayCollection());
        Phake::when($node0)->getBlocks()->thenReturn(new ArrayCollection());

        $node1 = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($node1)->getVersion()->thenReturn(1);
        Phake::when($node1)->getAreas()->thenReturn(new ArrayCollection());
        Phake::when($node1)->getBlocks()->thenReturn(new ArrayCollection());

        return array(
            array($node0, 'fr'),
            array($node1, 'en'),
        );
    }

    /**
     * @return array
     */
    public function provideNodeToDelete()
    {
        $nodeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');

        $nodesId = array('rootNodeId', 'childNodeId', 'otherChildNodeId');

        $nodes = array();
        foreach ($nodesId as $nodeId) {
            $nodes[$nodeId] = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
            Phake::when($nodes[$nodeId])->getNodeId()->thenReturn($nodeId);
        }

        $sons = new ArrayCollection();
        $sons->add($nodes[$nodesId[1]]);
        $sons->add($nodes[$nodesId[2]]);

        Phake::when($nodeRepository)->findByParentId($nodesId[0])->thenReturn($sons);
        Phake::when($nodeRepository)->findByParentId($nodesId[1])->thenReturn(new ArrayCollection());
        Phake::when($nodeRepository)->findByParentId($nodesId[2])->thenReturn(new ArrayCollection());

        return array(
            array($nodeRepository, $nodes[$nodesId[0]], $nodes)
        );
    }

    /**
     * test hydrateNodeFromNodeId
     */
    public function testHydrateNodeFromNodeId()
    {
        $newNode = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');

        $area = new Area();
        $areas = new ArrayCollection();
        $areas->add($area);
        $block = new Block();
        $blocks = new ArrayCollection();
        $blocks->add($block);
        $oldNodeId = 'oldNodeId';
        $oldNode = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
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
     * @return array
     */
    public function generateConsistencyNode()
    {
        return array(
            array(array($this->node, $this->node, $this->node)),
            array(array()),
        );
    }

    /**
     * @param array $nodes
     *
     * @dataProvider generateNoConsistencyNode
     */
    public function testNodeNoConsistency($nodes)
    {
        $this->assertFalse($this->manager->nodeConsistency($nodes));
    }

    /**
     * @return array
     */
    public function generateNoConsistencyNode()
    {
        return array(
            array(array($this->node, $this->node, $this->node)),
            array($this->node),
        );
    }
}
