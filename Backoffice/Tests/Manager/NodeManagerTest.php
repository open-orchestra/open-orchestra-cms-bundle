<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Saver\VersionableSaverInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\Backoffice\Manager\NodeManager;
use OpenOrchestra\ModelBundle\Document\Area;
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
    protected $area;
    protected $block;
    protected $blockTransverse;
    protected $status;
    protected $nodeClass;
    protected $areaClass;
    protected $contextManager;
    protected $nodeRepository;
    protected $siteRepository;
    protected $eventDispatcher;
    protected $statusRepository;
    protected $blockRepository;
    protected $documentManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $theme = Phake::mock('OpenOrchestra\ModelInterface\Model\ThemeInterface');
        Phake::when($theme)->getName()->thenReturn('fakeNameTheme');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getTheme()->thenReturn($theme);

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $this->area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        $this->block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $this->blockTransverse = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        $this->blockRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\BlockRepository');
        $this->versionableSaver = Phake::mock('OpenOrchestra\ModelInterface\Saver\VersionableSaverInterface');
        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        $this->documentManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->nodeClass = 'OpenOrchestra\ModelBundle\Document\Node';
        $this->areaClass = 'OpenOrchestra\ModelBundle\Document\Area';

        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($site);
        Phake::when($this->status)->isPublished()->thenReturn(true);
        Phake::when($this->status)->getLabels()->thenReturn(array());
        Phake::when($this->status)->getToRoles()->thenReturn(array());
        Phake::when($this->status)->getFromRoles()->thenReturn(array());
        Phake::when($this->statusRepository)->findOneByInitial()->thenReturn($this->status);
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn('fakeSiteId');
        Phake::when($this->contextManager)->getCurrentSiteDefaultLanguage()->thenReturn('fakeLanguage');
        Phake::when($this->block)->isTransverse()->thenReturn(false);
        Phake::when($this->blockTransverse)->isTransverse()->thenReturn(true);
        Phake::when($this->area)->getBlocks()->thenReturn(array($this->block, $this->blockTransverse));
        Phake::when($this->node)->getAreas()->thenReturn(array('fakeArea' => $this->area));
        Phake::when($this->node)->getTemplate()->thenReturn('fakeTemplate');
        Phake::when($this->blockRepository)->getDocumentManager()->thenReturn($this->documentManager);

        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->manager = new NodeManager(
            $this->versionableSaver,
            $this->nodeRepository,
            $this->siteRepository,
            $this->statusRepository,
            $this->blockRepository,
            $this->contextManager,
            $this->nodeClass,
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

        $lastNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($lastNode)->getVersion()->thenReturn($version);

        Phake::when($this->nodeRepository)->findVersion(Phake::anyParameters())->thenReturn($this->node);
        Phake::when($this->nodeRepository)->findInLastVersion(Phake::anyParameters())->thenReturn($lastNode);

        $newNode = $this->manager->duplicateNode($nodeId, $siteId, $language, $version);

        Phake::verify($newNode)->setCurrentlyPublished($this->status->isPublished());
        Phake::verify($newNode)->setVersion($version + 1);

        Phake::verify($this->blockRepository)->getDocumentManager();
        Phake::verify($this->versionableSaver)->saveDuplicated($newNode);
        Phake::verify($this->nodeRepository)->findVersion($nodeId, $language, $siteId, $version);
        Phake::verify($this->nodeRepository)->findInLastVersion($nodeId, $language, $siteId);
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
     * @param string          $language
     *
     * @dataProvider provideNodeAndLanguage
     */
    public function testCreateNewLanguageNode($language)
    {
        $alteredNode = $this->manager->createNewLanguageNode($this->node, $language);

        Phake::verify($alteredNode)->setVersion(1);
        Phake::verify($alteredNode)->setLanguage($language);
        Phake::verify($this->eventDispatcher)->dispatch(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideNodeAndLanguage()
    {
        return array(
            array('fr'),
            array('en'),
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
        Phake::when($this->nodeRepository)->findInLastVersion(Phake::anyParameters())->thenReturn($this->node);
        $newNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $newNode = $this->manager->hydrateNodeFromNodeId($newNode, 'fakeNodeId');

        Phake::verify($newNode)->setTemplate($this->node->getTemplate());

        Phake::verify($this->blockRepository)->getDocumentManager();
    }

    /**
     * Test initializeNode
     *
     * @param string               $language
     * @param string               $siteId
     * @param NodeInterface|null   $parentNode
     *
     * @dataProvider provideParentNode
     */
    public function testInitializeNode($language, $siteId, NodeInterface $parentNode = null)
    {
        Phake::when($this->nodeRepository)->findVersion(Phake::anyParameters())->thenReturn($parentNode);
        $node = $this->manager->initializeNode('fakeParentId', $language, $siteId);

        $this->assertInstanceOf($this->nodeClass, $node);
        $this->assertEquals($siteId, $node->getSiteId());
        $this->assertEquals($language, $node->getLanguage());
        $this->assertEquals(NodeInterface::THEME_DEFAULT, $node->getTheme());
        $this->assertTrue($node->hasDefaultSiteTheme());
        $this->assertEquals(0, $node->getOrder());
        if (is_null($parentNode)) {
            $this->assertSame(NodeInterface::ROOT_NODE_ID, $node->getNodeId());
            $this->assertSame(NodeInterface::TYPE_DEFAULT, $node->getNodeType());
        }
    }

    /**
     * Test initializeNode
     *
     * @param string               $language
     * @param string               $siteId
     * @param NodeInterface|null   $parentNode
     *
     * @dataProvider provideParentNode
     */
    public function testCreateRootNode($language, $siteId, NodeInterface $parentNode = null)
    {
        Phake::when($this->nodeRepository)->findVersion(Phake::anyParameters())->thenReturn($parentNode);
        $node = $this->manager->createRootNode($siteId, $language, 'fakeName', 'fakePattern', 'fakeTemplate');

        $this->assertInstanceOf($this->nodeClass, $node);
        $this->assertEquals($siteId, $node->getSiteId());
        $this->assertEquals($language, $node->getLanguage());
        $this->assertEquals(NodeInterface::THEME_DEFAULT, $node->getTheme());
        $this->assertTrue($node->hasDefaultSiteTheme());
        $this->assertEquals(0, $node->getOrder());
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

        return array(
            array('fake_language', 'fake_site_Id', $parentNode0),
            array('fake_language3', 'fake_site_Id3', null),
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
}
