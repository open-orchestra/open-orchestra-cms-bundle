<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\SiteEvents;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\UpdateNodeSiteAliasSubscriber;

/**
 * Test UpdateNodeSiteAliasSubscriberTest
 */
class UpdateNodeSiteAliasSubscriberTest extends AbstractBaseTestCase
{
    protected $subscriber;
    protected $nodeManager;
    protected $nodeRepository;
    protected $objectManager;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeManager = Phake::mock('OpenOrchestra\Backoffice\Manager\NodeManager');
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        Phake::when($this->nodeRepository)->findLastVersionByLanguage(Phake::anyParameters())->thenReturn(array(
            $this->node
        ));

        $this->subscriber = new UpdateNodeSiteAliasSubscriber(
            $this->nodeManager,
            $this->nodeRepository,
            $this->objectManager
        );
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
        $this->assertArrayHasKey(SiteEvents::SITE_UPDATE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * test updateNodeOnSiteAliasUpdate
     */
    public function testUpdateNodeOnSiteAliasUpdate()
    {
        $oldLanguage = 'fr';
        $newLanguage = 'es';
        $siteId = 'fakeSiteId';

        $event = Phake::mock('OpenOrchestra\ModelInterface\Event\SiteEvent');
        $oldAlias = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');

        Phake::when($oldAlias)->getLanguage()->thenReturn($oldLanguage);
        Phake::when($event)->getOldAliases()->thenReturn(array($oldAlias));
        Phake::when($site)->getLanguages()->thenReturn(array($newLanguage));
        Phake::when($site)->getSiteId()->thenReturn($siteId);
        Phake::when($event)->getSite()->thenReturn($site);

        $this->subscriber->updateNodeOnSiteAliasUpdate($event);

        Phake::verify($this->nodeRepository)->findLastVersionByLanguage($siteId, $oldLanguage);
        Phake::verify($this->nodeManager)->createNewLanguageNode($this->node, $newLanguage);
        Phake::verify($this->objectManager)->persist(Phake::anyParameters());
        Phake::verify($this->objectManager)->flush();
    }
}
