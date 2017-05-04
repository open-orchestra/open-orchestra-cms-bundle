<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\UpdateRouteDocumentSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\RedirectionEvents;
use OpenOrchestra\ModelInterface\SiteEvents;
use Phake;

/**
 * Test UpdateRouteDocumentSubscriberTest
 */
class UpdateRouteDocumentSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var UpdateRouteDocumentSubscriber
     */
    protected $subscriber;

    protected $routeDocumentManager;
    protected $objectManager;
    protected $status;
    protected $event;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node)->getStatus()->thenReturn($this->status);
        $this->event = Phake::mock('OpenOrchestra\ModelInterface\Event\NodeEvent');
        Phake::when($this->event)->getNode()->thenReturn($this->node);

        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');

        $this->routeDocumentManager = Phake::mock('OpenOrchestra\Backoffice\Manager\RouteDocumentManager');

        $this->subscriber = new UpdateRouteDocumentSubscriber($this->objectManager, $this->routeDocumentManager);
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
        $this->assertArrayHasKey(NodeEvents::NODE_CHANGE_STATUS, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(RedirectionEvents::REDIRECTION_CREATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(RedirectionEvents::REDIRECTION_UPDATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(SiteEvents::SITE_UPDATE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param boolean $published
     *
     * @dataProvider providePublishedStates
     */
    public function testUpdateDocument($published)
    {
        $route = Phake::mock('OpenOrchestra\ModelInterface\Model\RouteDocumentInterface');
        Phake::when($this->routeDocumentManager)->createForNode(Phake::anyParameters())->thenReturn(array($route));

        Phake::when($this->status)->isPublishedState()->thenReturn($published);
        Phake::when($this->event)->getPreviousStatus()->thenReturn($this->status);

        $this->subscriber->updateRouteDocument($this->event);
        $time = $published ? Phake::times(1) : Phake::never();
        Phake::verify($this->routeDocumentManager, $time)->clearForNode(Phake::anyParameters());
        Phake::verify($this->objectManager, $time)->persist($route);
        Phake::verify($this->objectManager, $time)->flush();
    }

    /**
     * @return array
     */
    public function providePublishedStates()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * Test with redirection
     */
    public function testCreateOrUpdateForRedirection()
    {
        $route = Phake::mock('OpenOrchestra\ModelInterface\Model\RouteDocumentInterface');
        $redirection = Phake::mock('OpenOrchestra\ModelInterface\Model\RedirectionInterface');
        $event = Phake::mock('OpenOrchestra\ModelInterface\Event\RedirectionEvent');
        Phake::when($event)->getRedirection()->thenReturn($redirection);

        Phake::when($this->routeDocumentManager)->createOrUpdateForRedirection(Phake::anyParameters())->thenReturn(array($route));

        $this->subscriber->createOrUpdateForRedirection($event);

        Phake::verify($this->objectManager)->persist($route);
        Phake::verify($this->objectManager)->flush();
    }

    /**
     * Test on site update
     */
    public function testUpdateRouteDocumentOnSiteUpdate()
    {
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $event = Phake::mock('OpenOrchestra\ModelInterface\Event\SiteEvent');
        Phake::when($event)->getSite()->thenReturn($site);

        $route = Phake::mock('OpenOrchestra\ModelInterface\Model\RouteDocumentInterface');
        Phake::when($this->routeDocumentManager)->createForSite(Phake::anyParameters())->thenReturn(array($route));

        $this->subscriber->updateRouteDocumentOnSiteUpdate($event);

        Phake::verify($this->routeDocumentManager)->clearForSite(Phake::anyParameters());
        Phake::verify($this->objectManager)->persist($route);
        Phake::verify($this->objectManager)->flush();
    }

    /**
     * Test on site delete
     */
    public function testDeleteRouteDocumentOnSiteDelete()
    {
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $event = Phake::mock('OpenOrchestra\ModelInterface\Event\SiteEvent');
        Phake::when($event)->getSite()->thenReturn($site);

        $this->subscriber->deleteRouteDocumentOnSiteDelete($event);

        Phake::verify($this->routeDocumentManager)->clearForSite($site);
    }

    /**
     * Test on deleteForRedirection
     */
    public function testDeleteForRedirection()
    {
        $redirection = Phake::mock('OpenOrchestra\ModelInterface\Model\RedirectionInterface');
        $event = Phake::mock('OpenOrchestra\ModelInterface\Event\RedirectionEvent');
        Phake::when($event)->getRedirection()->thenReturn($redirection);

        $this->subscriber->deleteForRedirection($event);

        Phake::verify($this->routeDocumentManager)->deleteForRedirection($redirection);
    }

    /**
     * Test on deleteRouteDocument
     */
    public function testDeleteRouteDocument()
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $event = Phake::mock('OpenOrchestra\ModelInterface\Event\NodeEvent');

        Phake::when($event)->getNode()->thenReturn($node);
        $this->subscriber->deleteRouteDocument($event);

        Phake::verify($this->routeDocumentManager)->clearForNode($node);
    }
}
