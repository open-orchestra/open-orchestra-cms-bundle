<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Saver\VersionableSaverInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\Backoffice\Manager\NodeManager;
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
class NodeManagerTest extends AbstractBaseTestCase
{
    /**
     * @var NodeManager
     */
    protected $manager;

    /**
     * @var VersionableSaverInterface
     */
    protected $versionableSaver;

    protected $node;
    protected $nodeClass;
    protected $areaClass;
    protected $areaManager;
    protected $blockManager;
    protected $contextManager;
    protected $nodeRepository;
    protected $siteRepository;
    protected $eventDispatcher;
    protected $statusRepository;
    protected $metaKeywords = 'fake keyword';
    protected $metaDescription = 'fake description';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $theme = Phake::mock('OpenOrchestra\ModelInterface\Model\ThemeInterface');
        Phake::when($theme)->getName()->thenReturn('fakeNameTheme');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getTheme()->thenReturn($theme);
        Phake::when($site)->getMetaKeywordsInLanguage(Phake::anyParameters())->thenReturn($this->metaKeywords);
        Phake::when($site)->getMetaDescriptionInLanguage(Phake::anyParameters())->thenReturn($this->metaDescription);
        Phake::when($site)->getMetaIndex()->thenReturn(true);
        Phake::when($site)->getMetaFollow()->thenReturn(true);

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        $this->versionableSaver = Phake::mock('OpenOrchestra\ModelInterface\Saver\VersionableSaverInterface');
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($site);
        $this->areaManager = Phake::mock('OpenOrchestra\Backoffice\Manager\AreaManager');
        $this->blockManager = Phake::mock('OpenOrchestra\Backoffice\Manager\BlockManager');
        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn('fakeSiteId');
        Phake::when($this->contextManager)->getCurrentSiteDefaultLanguage()->thenReturn('fakeLanguage');
        $this->nodeClass = 'OpenOrchestra\ModelBundle\Document\Node';
        $this->areaClass = 'OpenOrchestra\ModelBundle\Document\Area';

        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->manager = new NodeManager(
            $this->versionableSaver,
            $this->nodeRepository,
            $this->siteRepository,
            $this->statusRepository,
            $this->areaManager,
            $this->blockManager,
            $this->contextManager,
            $this->nodeClass,
            $this->areaClass,
            $this->eventDispatcher
        );
    }

    /**
     * test duplicateNode
     */
    public function testDuplicateNode()
    {
        $nodeId = 'fakeNodeId';
        $siteId = 'fakeSiteId';
        $language = 'fakeLanguage';
        $version = 1;

        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($area)->getBlocks()->thenReturn(array());
        Phake::when($area)->getAreas()->thenReturn(array());

        $rootArea = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($rootArea)->getAreas()->thenReturn(array($area));

        $node0 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node0)->getArea()->thenReturn($rootArea);
        Phake::when($node0)->getBlocks()->thenReturn(array(2 => $block));
        $node2 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');

        Phake::when($this->nodeRepository)->findVersion(Phake::anyParameters())->thenReturn($node0);
        Phake::when($this->nodeRepository)->findInLastVersion(Phake::anyParameters())->thenReturn($node2);
        Phake::when($this->statusRepository)->findOneByInitial()->thenReturn($status);

        $newNode = $this->manager->duplicateNode($nodeId, $siteId, $language, $version);

        Phake::verify($this->nodeRepository)->findVersion($nodeId, $language, $siteId, $version);
        Phake::verify($this->versionableSaver)->saveDuplicated($node0);
        Phake::verify($newNode)->setBlock(2, $block);
        Phake::verify($newNode)->setArea($rootArea);
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
        Phake::verify($alteredNode)->setMetaKeywords($this->metaKeywords);
        Phake::verify($alteredNode)->setMetaDescription($this->metaDescription);
        Phake::verify($this->eventDispatcher)->dispatch(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideNodeAndLanguage()
    {
        $rootArea = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($rootArea)->getAreas()->thenReturn(array());

        $node0 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node0)->getVersion()->thenReturn(0);
        Phake::when($node0)->getArea()->thenReturn($rootArea);
        Phake::when($node0)->getBlocks()->thenReturn(new ArrayCollection());

        $node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node1)->getVersion()->thenReturn(1);
        Phake::when($node1)->getArea()->thenReturn($rootArea);
        Phake::when($node1)->getBlocks()->thenReturn(new ArrayCollection());

        return array(
            array($node0, 'fr'),
            array($node1, 'en'),
        );
    }

    /**
     * @param string $nodeId
     * @param string $siteId
     * @param string $language
     *
     * @dataProvider provideNodeAndSiteAndLanguage
     */
    public function testCreateNewErrorNode($nodeId, $siteId, $language)
    {
        $newNode = $this->manager->createNewErrorNode($nodeId, $siteId, $language);

        $this->assertEquals($nodeId, $newNode->getNodeId());
        $this->assertEquals(ReadNodeInterface::TYPE_ERROR, $newNode->getNodeType());
        $this->assertEquals($siteId, $newNode->getSiteId());
        $this->assertEquals($nodeId, $newNode->getRoutePattern());
        $this->assertEquals($nodeId, $newNode->getName());
        $this->assertEquals($language, $newNode->getLanguage());
        $this->assertEquals(false, $newNode->isInFooter());
        $this->assertEquals(false, $newNode->isInMenu());
        $this->assertEquals(1, $newNode->getVersion());

        $area = $newNode->getArea();
        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\AreaInterface', $area);
        $this->assertEquals(AreaInterface::TYPE_ROOT, $area->getAreaType());
        $this->assertEquals(AreaInterface::ROOT_AREA_ID, $area->getAreaId());
        $this->assertEquals(AreaInterface::ROOT_AREA_LABEL, $area->getLabel());

        Phake::verify($this->eventDispatcher)->dispatch(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideNodeAndSiteAndLanguage()
    {
        return array(
            array('errorPage404', '2', 'fr'),
            array('errorPage503', '1', 'en')
        );
    }

    /**
     * Test deleteTree
     */
    public function testDeleteTree()
    {
        $nodePath = 'nodePath';
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getPath()->thenReturn($nodePath);
        Phake::when($node)->isDeleted()->thenReturn(false);

        $nodes = new ArrayCollection();
        $nodes->add($node);

        $subNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($subNode)->isDeleted()->thenReturn(false);
        $subNodes = new ArrayCollection();
        $subNodes->add($subNode);
        $subNodes->add($subNode);

        $siteId = $this->contextManager->getCurrentSiteId();
        Phake::when($this->nodeRepository)->findByIncludedPathAndSiteId($nodePath, $siteId)->thenReturn($subNodes);

        $this->manager->deleteTree($nodes);

        Phake::verify($node, Phake::times(1))->setDeleted(true);
        Phake::verify($subNode, Phake::times(2))->setDeleted(true);
        Phake::verify($node, Phake::times(1))->setOrder(NodeInterface::DELETED_ORDER);
        Phake::verify($subNode, Phake::times(2))->setOrder(NodeInterface::DELETED_ORDER);
        Phake::verify($this->eventDispatcher, Phake::times(3))->dispatch(Phake::anyParameters());
    }

    /**
     * test hydrateNodeFromNodeId
     */
    public function testHydrateNodeFromNodeId()
    {
        $newNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        $rootArea = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($rootArea)->getAreas()->thenReturn(array());

        $block = new Block();
        $blocks = new ArrayCollection();
        $blocks->set(5, $block);
        $oldNodeId = 'oldNodeId';
        $oldNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($oldNode)->getArea()->thenReturn($rootArea);
        Phake::when($oldNode)->getBlocks()->thenReturn($blocks);
        Phake::when($this->nodeRepository)->findInLastVersion(Phake::anyParameters())->thenReturn($oldNode);

        $this->manager->hydrateNodeFromNodeId($newNode, $oldNodeId);

        Phake::verify($newNode)->setBlock(5, $block);
        Phake::verify($newNode)->setArea($rootArea);
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
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $rootArea = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($node)->getArea()->thenReturn($rootArea);

        return array(
            array(array($node, $node, $node)),
            array(array()),
        );
    }

    /**
     * Test initializeNode
     *
     * @param string               $language
     * @param string               $siteId
     * @param NodeInterface|null   $parentNode
     * @param StatusInterface|null $status
     *
     * @dataProvider provideParentNode
     */
    public function testInitializeNode($language, $siteId, NodeInterface $parentNode = null, $status = null)
    {
        Phake::when($this->nodeRepository)->findVersion(Phake::anyParameters())->thenReturn($parentNode);
        Phake::when($this->statusRepository)->findOneByEditable()->thenReturn($status);
        $node = $this->manager->initializeNode('fakeParentId', $language, $siteId);

        $this->assertInstanceOf($this->nodeClass, $node);
        $this->assertEquals($siteId, $node->getSiteId());
        $this->assertEquals($language, $node->getLanguage());
        $this->assertEquals(NodeInterface::THEME_DEFAULT, $node->getTheme());
        $this->assertTrue($node->hasDefaultSiteTheme());
        $this->assertEquals($this->metaKeywords, $node->getMetaKeywords());
        $this->assertEquals($this->metaDescription, $node->getMetaDescription());
        $this->assertEquals(0, $node->getOrder());
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
            array('fake_language', 'fake_site_Id', $parentNode0, null),
            array('fake_language2', 'fake_site_Id2', $parentNode1, EmbedStatus::createFromStatus($status)),
            array('fake_language3', 'fake_site_Id3', null, EmbedStatus::createFromStatus($status)),
        );
    }

    /**
     * Test getNewNodeOrder
     *
     * @param NodeInterface|null $node
     * @param int                $expectedOrder
     *
     * @dataProvider provideGreatestOrderedNode
     */
    public function testGetNewNodeOrder($node, $expectedOrder)
    {
        Phake::when($this->nodeRepository)->findOneByParentWithGreatestOrder(Phake::anyParameters())->thenReturn($node);

        $node = $this->manager->initializeNode('fakeParentId', 'fakeLanguage', 'fakeId');

        $this->assertEquals($expectedOrder, $node->getOrder());
    }

    /**
     * @return array
     */
    public function provideGreatestOrderedNode()
    {
        $node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node1)->getOrder()->thenReturn(1);

        $node2 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node2)->getOrder()->thenReturn(4);

        return array(
            array($node1, 2),
            array($node2, 5),
            array(null, 0),
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
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $areaRoot = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($node)->getArea()->thenReturn($areaRoot);

        Phake::when($this->areaManager)->areaConsistency(Phake::anyParameters())->thenReturn($areaConsistency);
        Phake::when($this->blockManager)->blockConsistency(Phake::anyParameters())->thenReturn($blockConsistency);

        $this->assertFalse($this->manager->nodeConsistency(array($node)));
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

        $areaRoot = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($areaRoot)->getAreas()->thenReturn(new ArrayCollection(array($area1, $area2)));

        $newNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($newNode)->getId()->thenReturn($newId);
        Phake::when($newNode)->getBlock(0)->thenReturn($block2);
        Phake::when($newNode)->getBlock(1)->thenReturn($block3);
        Phake::when($newNode)->getArea()->thenReturn($areaRoot);

        $oldNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($oldNode)->getId()->thenReturn($oldId);

        Phake::when($this->nodeRepository)
            ->findInLastVersion(Phake::anyParameters())->thenReturn($transverseNode);

        $this->manager->updateBlockReferences($oldNode, $newNode);

        Phake::verify($this->nodeRepository)->findInLastVersion(Phake::anyParameters());
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
        Phake::when($this->nodeRepository)->findByNodeAndSite('son',$siteId)->thenReturn($sons);

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

    /**
     * @param string $language
     * @param string $siteId
     *
     * @dataProvider provideLanguageAndSite
     */
    public function testCreateTransverseNode($language, $siteId)
    {
        $node = $this->manager->createTransverseNode($language, $siteId);

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\NodeInterface', $node);
        $this->assertSame($siteId, $node->getSiteId());
        $this->assertSame(NodeInterface::TRANSVERSE_NODE_ID, $node->getNodeId());
        $this->assertSame(NodeInterface::TRANSVERSE_NODE_ID, $node->getName());
        $this->assertSame(NodeInterface::TRANSVERSE_BO_LABEL, $node->getBoLabel());
        $this->assertSame(NodeInterface::TYPE_TRANSVERSE, $node->getNodeType());
        $this->assertSame(1, $node->getVersion());
        $this->assertSame($language, $node->getLanguage());
        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\AreaInterface', $node->getArea());
        $this->assertSame(AreaInterface::TYPE_ROOT, $node->getArea()->getAreaType());
    }

    /**
     * @return array
     */
    public function provideLanguageAndSite()
    {
        return array(
            array('fr', '1'),
            array('en', '1'),
            array('fr', '2'),
            array('en', '2'),
        );
    }
}
