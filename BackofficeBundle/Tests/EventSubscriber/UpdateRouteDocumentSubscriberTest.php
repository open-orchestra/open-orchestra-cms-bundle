<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use OpenOrchestra\BackofficeBundle\EventSubscriber\UpdateRouteDocumentSubscriber;
use OpenOrchestra\ModelInterface\NodeEvents;
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
    }

    /**
     * Test when node not published
     */
    public function testUpdateDocumentWithNoPublishedNode()
    {
        Phake::when($this->status)->isPublished()->thenReturn(false);

        $this->subscriber->updateRouteDocument($this->event);

        Phake::verify($this->objectManager, Phake::never())->persist(Phake::anyParameters());
        Phake::verify($this->objectManager, Phake::never())->flush(Phake::anyParameters());
    }

    /**
     * Test when the node has been published
     */
    public function testUpdateDocumentWithPublishedNode()
    {
        $route = Phake::mock('OpenOrchestra\ModelInterface\Model\RouteDocumentInterface');
        Phake::when($this->routeDocumentManager)->createForNode(Phake::anyParameters())->thenReturn(array($route));
        Phake::when($this->routeDocumentManager)->clearForNode(Phake::anyParameters())->thenReturn(array($route));
        Phake::when($this->status)->isPublished()->thenReturn(true);

        $this->subscriber->updateRouteDocument($this->event);

        Phake::verify($this->objectManager)->persist($route);
        Phake::verify($this->objectManager)->remove($route);
        Phake::verify($this->objectManager)->flush();
    }
}
