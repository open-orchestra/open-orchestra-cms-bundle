<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\UpdateSiteAliasRedirectionSiteSubscriber;
use OpenOrchestra\ModelInterface\SiteEvents;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Test UpdateSiteAliasRedirectionSiteSubscriberTest
 */
class UpdateSiteAliasRedirectionSiteSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var UpdateSiteAliasRedirectionSiteSubscriber
     */
    protected $subscriber;
    protected $event;
    protected $site;
    protected $objectManager;
    protected $nodeRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        $siteAlias0 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        $siteAlias1 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');

        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($this->site)->getAliases()->thenReturn(new ArrayCollection(array($siteAlias0, $siteAlias1)));

        $this->event = Phake::mock('OpenOrchestra\ModelInterface\Event\SiteEvent');
        Phake::when($this->event)->getSite()->thenReturn($this->site);
        Phake::when($this->event)->getOldAliases()->thenReturn(new ArrayCollection());

        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->redirectionManager = Phake::mock('OpenOrchestra\Backoffice\Manager\RedirectionManager');

        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        Phake::when($this->nodeRepository)->findLastVersionBySiteId(Phake::anyParameters())->thenReturn(array($node, $node));
        Phake::when($this->nodeRepository)->findLastVersionByType(Phake::anyParameters())->thenReturn(array($node, $node));

        $this->subscriber = new UpdateSiteAliasRedirectionSiteSubscriber($this->objectManager, $this->redirectionManager, $this->nodeRepository);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test event subscribed
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(SiteEvents::SITE_CREATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(SiteEvents::SITE_UPDATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(SiteEvents::SITE_DELETE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * test updateSiteAliasesIndexOnSiteCreate
     */
    public function testUpdateSiteAliasesIndexOnSiteCreate()
    {
        $this->subscriber->updateSiteAliasesIndexOnSiteCreate($this->event);

        Phake::verify($this->site, Phake::times(2))->removeAlias(Phake::anyParameters());
        Phake::verify($this->site, Phake::times(2))->addAlias(Phake::anyParameters());
        Phake::verify($this->objectManager, Phake::times(1))->persist(Phake::anyParameters());
        Phake::verify($this->objectManager, Phake::times(1))->flush(Phake::anyParameters());
    }

    /**
     * test updateRedirectionOnSiteUpdate
     */
    public function testUpdateRedirectionOnSiteUpdate()
    {
        $this->subscriber->updateRedirectionOnSiteUpdate($this->event);

        Phake::verify($this->redirectionManager, Phake::times(2))->generateRedirectionForNode(Phake::anyParameters());
    }

    /**
     * test deleteRedirectionOnSiteDelete
     */
    public function testDeleteRedirectionOnSiteDelete()
    {
        $this->subscriber->deleteRedirectionOnSiteDelete($this->event);

        Phake::verify($this->redirectionManager, Phake::times(2))->deleteRedirection(Phake::anyParameters());
    }
}
