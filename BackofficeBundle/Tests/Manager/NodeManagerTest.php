<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Manager;

use OpenOrchestra\BackofficeBundle\Manager\NodeManager;
use OpenOrchestra\ModelBundle\Document\Area;
use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\EmbedStatus;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Class NodeManagerTest
 */
class NodeManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeManager
     */
    protected $manager;

    protected $node;
    protected $nodeClass;
    protected $areaManager;
    protected $blockManager;
    protected $contextManager;
    protected $nodeRepository;
    protected $siteRepository;
    protected $statusRepository;
    protected $eventDispatcher;
    protected $nodeManager;
    /**
     * Set up the test
     */
    public function setUp()
    {
        $theme = Phake::mock('OpenOrchestra\ModelInterface\Model\ThemeInterface');
        Phake::when($theme)->getName()->thenReturn('fakeNameTheme');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getTheme()->thenReturn($theme);
        Phake::when($site)->getMetaKeywords()->thenReturn('fake keyword');
        Phake::when($site)->getMetaDescription()->thenReturn('fake description');
        Phake::when($site)->getMetaIndex()->thenReturn(true);
        Phake::when($site)->getMetaFollow()->thenReturn(true);

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        $this->nodeManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\NodeManagerInterface');
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($site);
        $this->areaManager = Phake::mock('OpenOrchestra\BackofficeBundle\Manager\AreaManager');
        $this->blockManager = Phake::mock('OpenOrchestra\BackofficeBundle\Manager\BlockManager');
        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn('fakeSiteId');
        Phake::when($this->contextManager)->getCurrentSiteDefaultLanguage()->thenReturn('fakeLanguage');
        $this->nodeClass = 'OpenOrchestra\ModelBundle\Document\Node';

        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->manager = new NodeManager($this->nodeManager, $this->nodeRepository, $this->siteRepository, $this->statusRepository, $this->areaManager, $this->blockManager, $this->contextManager, $this->nodeClass, $this->eventDispatcher);
    }

    /**
     * test duplicateNode
     */
    public function testDuplicateNode()
    {
        $nodeId = 'fakeNodeId';
        $siteId = 'fakeSiteId';
        $language = 'fakeLanguage';
        $statusId = 'fakeStatusId';

        $node0 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node1)->getAreas()->thenReturn(array());
        $node2 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->getId()->thenReturn($statusId);

        Phake::when($this->nodeRepository)->findOneByNodeIdAndLanguageAndVersionAndSiteId($nodeId, $language, $siteId)->thenReturn($node0);
        Phake::when($this->nodeManager)->duplicateNode($nodeId, $siteId, $language, $statusId)->thenReturn($node1);
        Phake::when($this->nodeRepository)->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(Phake::anyParameters())->thenReturn($node2);
        Phake::when($this->statusRepository)->findOneByInitial()->thenReturn($status);

        $alteredNode = $this->manager->duplicateNode($nodeId, $siteId, $language);

        Phake::verify($this->nodeRepository)->findOneByNodeIdAndLanguageAndVersionAndSiteId($nodeId, $language, $siteId);
        Phake::verify($this->nodeManager)->duplicateNode($nodeId, $siteId, $language, $statusId);
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
        Phake::verify($this->eventDispatcher)->dispatch(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideNodeAndLanguage()
    {
        $node0 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node0)->getVersion()->thenReturn(0);
        Phake::when($node0)->getAreas()->thenReturn(new ArrayCollection());
        Phake::when($node0)->getBlocks()->thenReturn(new ArrayCollection());

        $node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
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
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getNodeId()->thenReturn($nodeId);
        $nodes = new ArrayCollection();
        $nodes->add($node);
        $nodes->add($node);

        $sonId = 'sonId';
        $son = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($son)->getNodeId()->thenReturn($sonId);
        $sons = new ArrayCollection();
        $sons->add($son);
        $sons->add($son);

        $siteId = $this->contextManager->getCurrentSiteId();
        Phake::when($this->nodeRepository)->findByParentIdAndSiteId($nodeId, $siteId)->thenReturn($sons);
        Phake::when($this->nodeRepository)->findByParentIdAndSiteId($sonId, $siteId)->thenReturn(new ArrayCollection());

        $this->manager->deleteTree($nodes);

        Phake::verify($node, Phake::times(2))->setDeleted(true);
        Phake::verify($son, Phake::times(2))->setDeleted(true);
        Phake::verify($this->nodeRepository)->findByParentIdAndSiteId($nodeId, $siteId);
        Phake::verify($this->nodeRepository)->findByParentIdAndSiteId($sonId, $siteId);
        Phake::verify($this->eventDispatcher, Phake::times(2))->dispatch(Phake::anyParameters());
    }

    /**
     * test hydrateNodeFromNodeId
     */
    public function testHydrateNodeFromNodeId()
    {
        $newNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        $area = new Area();
        $areas = new ArrayCollection();
        $areas->add($area);
        $block = new Block();
        $blocks = new ArrayCollection();
        $blocks->add($block);
        $oldNodeId = 'oldNodeId';
        $oldNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
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
        $areaContainer = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaContainerInterface');

        return array(
            array(array($areaContainer, $areaContainer, $areaContainer)),
            array(array()),
        );
    }

    /**
     * Test initializeNewNode
     *
     * @param NodeInterface|null   $parentNode
     * @param StatusInterface|null $status
     *
     * @dataProvider provideParentNode
     */
    public function testInitializeNewNode(NodeInterface $parentNode = null, $status = null)
    {
        Phake::when($this->nodeRepository)->findOneByNodeIdAndLanguageAndVersionAndSiteId(Phake::anyParameters())->thenReturn($parentNode);
        Phake::when($this->statusRepository)->findOneByEditable()->thenReturn($status);
        $node = $this->manager->initializeNewNode('fakeParentId');

        $this->assertInstanceOf($this->nodeClass, $node);
        $this->assertEquals('fakeSiteId', $node->getSiteId());
        $this->assertEquals('fakeLanguage', $node->getLanguage());
        $this->assertEquals('fakeNameTheme', $node->getTheme());
        $this->assertEquals('fake keyword', $node->getMetaKeywords());
        $this->assertEquals('fake description', $node->getMetaDescription());
        $this->assertEquals($status, $node->getStatus());
        $this->assertEquals(true, $node->getMetaIndex());
        $this->assertEquals(true, $node->getMetaFollow());
        if (is_null($parentNode)) {
            $this->assertSame(NodeInterface::ROOT_NODE_ID, $node->getNodeId());
            $this->assertSame(NodeInterface::TYPE_DEFAULT, $node->getNodeType());
        }
    }

    /**
     * @return array
     */
    public function provideParentNode()
    {
        $parentNode0 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($parentNode0)->getNodeId()->thenReturn('fakeId');
        Phake::when($parentNode0)->getNodeType()->thenReturn(NodeInterface::TYPE_DEFAULT);

        $parentNode1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($parentNode1)->getNodeId()->thenReturn(NodeInterface::TRANSVERSE_NODE_ID);
        Phake::when($parentNode1)->getNodeType()->thenReturn(NodeInterface::TYPE_TRANSVERSE);
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->getToRoles()->thenReturn(array());
        Phake::when($status)->getFromRoles()->thenReturn(array());

        return array(
            array($parentNode0, null),
            array($parentNode1, EmbedStatus::createFromStatus($status)),
            array(null, EmbedStatus::createFromStatus($status)),
        );
    }

    /**
     * @param bool $areaConsistency
     * @param bool $blockConsistency
     *
     * @dataProvider provideConsistency
     */
    public function testNodeNoConsistency($areaConsistency, $blockConsistency)
    {
        $areaContainer = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaContainerInterface');
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
        $block1 = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block1)->getAreas()
            ->thenReturn(array(array('nodeId' => 0, 'areaId' => 'main'), array('nodeId' => $oldId, 'areaId' => 'main')));

        $block2 = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block2)->getAreas()->thenReturn(array(array('nodeId' => $oldId, 'areaId' => 'main')));

        $block3 = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block3)->getAreas()->thenReturn(array(array('nodeId' => 0, 'areaId' => 'main')));

        $area1 = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($area1)->getBlocks()
            ->thenReturn(array(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0)));
        Phake::when($area1)->getAreaId()->thenReturn('main');

        $area2 = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($area2)->getBlocks()
            ->thenReturn(array(array('nodeId' => 'oldNode', 'blockId' => 0), array('nodeId' => 0, 'blockId' => 1)));

        $transverseNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($transverseNode)->getBlock(0)->thenReturn($block1);

        $newNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($newNode)->getId()->thenReturn($newId);
        Phake::when($newNode)->getBlock(0)->thenReturn($block2);
        Phake::when($newNode)->getBlock(1)->thenReturn($block3);
        Phake::when($newNode)->getAreas()->thenReturn(new ArrayCollection(array($area1, $area2)));

        $oldNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
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

    /**
     * @param int    $position
     * @param string $nodeId
     * @param string $parentPath
     *
     * @dataProvider providePositionAndNodeIdAndParentPath
     */
    public function testOrderNodeChildren($position, $nodeId, $parentPath)
    {
        $sonNodeId = 'son';
        $orderedNode = array($position => $sonNodeId);

        $parentNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($parentNode)->getNodeId()->thenReturn($nodeId);
        Phake::when($parentNode)->getPath()->thenReturn($parentPath);


        $sons = new ArrayCollection();
        $sons->add($this->node);
        $sons->add($this->node);
        $sons->add($this->node);
        $sons->add($this->node);

        $siteId = $this->contextManager->getCurrentSiteId();
        Phake::when($this->nodeRepository)->findByNodeIdAndSiteId('son',$siteId)->thenReturn($sons);

        $this->manager->orderNodeChildren($orderedNode, $parentNode);

        Phake::verify($this->node, Phake::times(4))->setParentId($nodeId);
        Phake::verify($this->node, Phake::times(4))->setOrder($position);
        Phake::verify($this->node, Phake::times(4))->setPath($parentPath . '/' . $sonNodeId);
        Phake::verify($this->eventDispatcher)->dispatch(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function providePositionAndNodeIdAndParentPath()
    {
        return array(
            array(0, 'root', ''),
            array(3, 'test', '/test'),
            array(4, 'fixture', '/test/fixture'),
        );
    }
}
