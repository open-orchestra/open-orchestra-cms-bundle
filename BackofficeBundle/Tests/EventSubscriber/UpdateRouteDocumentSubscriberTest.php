<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use OpenOrchestra\BackofficeBundle\EventSubscriber\UpdateRouteDocumentSubscriber;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\RedirectionEvents;
use OpenOrchestra\ModelInterface\SiteEvents;
use Phake;

/**
 * Test UpdateRouteDocumentSubscriberTest
 */
class UpdateRouteDocumentSubscriberTest extends \PHPUnit_Framework_TestCase
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

        $this->routeDocumentManager = Phake::mock('OpenOrchestra\BackofficeBundle\Manager\RouteDocumentManager');

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
        Phake::when($this->routeDocumentManager)->clearForNode(Phake::anyParameters())->thenReturn(array($route));

        Phake::when($this->status)->isPublished()->thenReturn($published);

        $this->subscriber->updateRouteDocument($this->event);

        Phake::verify($this->objectManager)->persist($route);
        Phake::verify($this->objectManager)->remove($route);
        Phake::verify($this->objectManager)->flush();
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
        Phake::verify($this->objectManager)->flush($route);
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
        Phake::when($this->routeDocumentManager)->clearForSite(Phake::anyParameters())->thenReturn(array($route));

        $this->subscriber->updateRouteDocumentOnSiteUpdate($event);

        Phake::verify($this->objectManager)->persist($route);
        Phake::verify($this->objectManager)->remove($route);
        Phake::verify($this->objectManager)->flush();
    }

    /**
     * Test on deleteForRedirection
     */
    public function testDeleteForRedirection()
    {
        $redirection = Phake::mock('OpenOrchestra\ModelInterface\Model\RedirectionInterface');
        $event = Phake::mock('OpenOrchestra\ModelInterface\Event\RedirectionEvent');
        $route = Phake::mock('OpenOrchestra\ModelInterface\Model\RouteDocumentInterface');

        Phake::when($event)->getRedirection()->thenReturn($redirection);
        Phake::when($this->routeDocumentManager)->deleteForRedirection(Phake::anyParameters())->thenReturn(array($route, $route));

        $this->subscriber->deleteForRedirection($event);

        Phake::verify($this->objectManager, Phake::times(2))->remove($route);
        Phake::verify($this->objectManager)->flush();
    }
}
