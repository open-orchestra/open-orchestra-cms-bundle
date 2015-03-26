<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use OpenOrchestra\BackofficeBundle\Manager\RedirectionManager;

/**
 * Test RedirectionManagerTest
 */
class RedirectionManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RedirectionManager
     */
    protected $manager;

    protected $site;
    protected $siteAlias1;
    protected $siteAlias2;
    protected $siteAlias3;
    protected $siteId = ';';
    protected $siteRepository;
    protected $contextManager;
    protected $documentManager;
    protected $localeEn = 'en';
    protected $localeFr = 'fr';
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
        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($this->site)->getAliases()->thenReturn($siteAliases);
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($this->site);

        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->manager = new RedirectionManager(
            $this->redirectionClass,
            $this->contextManager,
            $this->documentManager,
            $this->eventDispatcher,
            $this->siteRepository
        );
    }

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
}
