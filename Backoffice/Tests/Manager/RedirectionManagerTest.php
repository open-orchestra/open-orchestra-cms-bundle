<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Manager\RedirectionManager;

/**
 * Test RedirectionManagerTest
 */
class RedirectionManagerTest extends AbstractBaseTestCase
{
    /**
     * @var RedirectionManager
     */
    protected $manager;

    protected $site;
    protected $siteAlias1;
    protected $siteAlias2;
    protected $siteAlias3;
    protected $siteId = 'fakeSiteId';
    protected $siteRepository;
    protected $nodeRepository;
    protected $nodeSource;
    protected $redirectionRepository;
    protected $contextManager;
    protected $documentManager;
    protected $localeEn = 'en';
    protected $localeFr = 'fr';
    protected $nodeId = 'fakeNodeId';
    protected $nodeRoutePattern = 'fakeRoutePattern';
    protected $otherNodeRoutePattern = 'otherFakeRoutePattern';

    protected $eventDispatcher;
    protected $redirectionClass;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->redirectionClass = 'OpenOrchestra\ModelBundle\Document\Redirection';

        $this->contextManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn($this->siteId);

        $this->siteAlias1 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($this->siteAlias1)->getLanguage()->thenReturn($this->localeFr);
        $this->siteAlias2 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($this->siteAlias2)->getLanguage()->thenReturn($this->localeFr);
        Phake::when($this->siteAlias2)->getPrefix()->thenReturn($this->localeFr);
        $this->siteAlias3 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($this->siteAlias3)->getLanguage()->thenReturn($this->localeEn);
        $siteAliases = new ArrayCollection(array($this->siteAlias1, $this->siteAlias2, $this->siteAlias3));

        $redirection1 = Phake::mock('OpenOrchestra\ModelInterface\Model\RedirectionInterface');
        $redirection2 = Phake::mock('OpenOrchestra\ModelInterface\Model\RedirectionInterface');
        $redirection3 = Phake::mock('OpenOrchestra\ModelInterface\Model\RedirectionInterface');
        $redirections = new ArrayCollection(array($redirection1, $redirection2, $redirection3));

        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($this->site)->getAliases()->thenReturn($siteAliases);

        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($this->site);

        $this->redirectionRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\RedirectionRepositoryInterface');
        Phake::when($this->redirectionRepository)->findByNode(Phake::anyParameters())->thenReturn($redirections);

        $this->nodeSource = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->nodeSource)->getNodeId()->thenReturn($this->nodeId);
        Phake::when($this->nodeSource)->getLanguage()->thenReturn($this->localeFr);
        Phake::when($this->nodeSource)->getSideId()->thenReturn($this->siteId);
        Phake::when($this->nodeSource)->getRoutePattern()->thenReturn($this->nodeRoutePattern);

        $nodeCopy = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($nodeCopy)->getNodeId()->thenReturn($this->nodeId);
        Phake::when($nodeCopy)->getLanguage()->thenReturn($this->localeFr);
        Phake::when($nodeCopy)->getSideId()->thenReturn($this->siteId);
        Phake::when($nodeCopy)->getRoutePattern()->thenReturn($this->otherNodeRoutePattern);

        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        Phake::when($this->nodeRepository)->findPublishedSortedByVersion(Phake::anyParameters())->thenReturn(array(
            $this->nodeSource,
            $nodeCopy
        ));
        Phake::when($this->nodeRepository)->findOneCurrentlyPublished(Phake::anyParameters())->thenReturn(null);

        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->manager = new RedirectionManager(
            $this->redirectionClass,
            $this->contextManager,
            $this->documentManager,
            $this->eventDispatcher,
            $this->siteRepository,
            $this->nodeRepository,
            $this->redirectionRepository
        );
    }

    /**
     * test createRedirection
     */
    public function testCreateRedirection()
    {
        $nodeId = 'nodeId';
        $this->manager->createRedirection('test/test', $nodeId, $this->localeFr);

        Phake::verify($this->siteAlias1)->getPrefix();
        Phake::verify($this->siteAlias2)->getPrefix();
        Phake::verify($this->siteAlias3, Phake::never())->getPrefix();
        Phake::verify($this->documentManager, Phake::times(2))->persist(Phake::anyParameters());
        Phake::verify($this->documentManager, Phake::times(2))->flush(Phake::anyParameters());
        Phake::verify($this->eventDispatcher, Phake::times(2))->dispatch(Phake::anyParameters());
    }

    /**
     * test deleteRedirection
     */
    public function testDeleteRedirection()
    {
        $this->manager->deleteRedirection('fakeNodeId', 'fakeLanguage');

        Phake::verify($this->documentManager, Phake::times(3))->remove(Phake::anyParameters());
        Phake::verify($this->documentManager, Phake::times(3))->flush(Phake::anyParameters());
        Phake::verify($this->eventDispatcher, Phake::times(3))->dispatch(Phake::anyParameters());
    }

    /**
     * test updateRedirection
     */
    public function testUpdateRedirection()
    {
        $this->manager->updateRedirection('fakeNodeId', 'fakeLanguage');

        Phake::verify($this->eventDispatcher, Phake::times(3))->dispatch(Phake::anyParameters());
    }

    public function testGenerateRedirectionForNode() {
        $this->manager->generateRedirectionForNode($this->nodeSource);
        Phake::verify($this->documentManager, Phake::times(2))->persist(Phake::anyParameters());
        Phake::verify($this->documentManager, Phake::times(5))->flush(Phake::anyParameters());
        Phake::verify($this->eventDispatcher, Phake::times(5))->dispatch(Phake::anyParameters());
    }
}
