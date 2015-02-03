<?php

namespace PHPOrchestra\BackofficeBundle\Test\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use PHPOrchestra\BackofficeBundle\EventSubscriber\UpdateChildNodePathSubscriber;
use PHPOrchestra\ModelInterface\NodeEvents;

/**
 * Class UpdateChildNodePathSubscriberTest
 */
class UpdateChildNodePathSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateChildNodePathSubscriber
     */
    protected $subscriber;

    protected $nodeRepository;
    protected $eventDispatcher;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('PHPOrchestra\ModelInterface\Repository\NodeRepositoryInterface');

        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        Phake::when($container)->get('event_dispatcher')->thenReturn($this->eventDispatcher);

        $this->subscriber = new UpdateChildNodePathSubscriber($this->nodeRepository, $container);
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
        $this->assertArrayHasKey(NodeEvents::PATH_UPDATED, $this->subscriber->getSubscribedEvents());
    }

    public function testUpdatePath()
    {
        $parentNodeId = 'parent';
        $parentPath = 'parentPath';
        $son1NodeId = 'son1NodeId';
        $son2NodeId = 'son2NodeId';

        $parent = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($parent)->getNodeId()->thenReturn($parentNodeId);
        Phake::when($parent)->getPath()->thenReturn($parentPath);
        $son1 = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($son1)->getNodeId()->thenReturn($son1NodeId);
        $son2 = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($son2)->getNodeId()->thenReturn($son2NodeId);
        $son3 = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($son3)->getNodeId()->thenReturn($son2NodeId);
        $sons = new ArrayCollection();
        $sons->add($son1);
        $sons->add($son2);
        $sons->add($son3);
        Phake::when($this->nodeRepository)->findByParentIdAndSiteId($parentNodeId)->thenReturn($sons);

        $event = Phake::mock('PHPOrchestra\ModelInterface\Event\NodeEvent');
        Phake::when($event)->getNode()->thenReturn($parent);

        $this->subscriber->updatePath($event);

        Phake::verify($son1)->setPath($parentPath . '/' . $son1NodeId);
        Phake::verify($son2)->setPath($parentPath . '/' . $son2NodeId);
        Phake::verify($son3)->setPath($parentPath . '/' . $son2NodeId);

        Phake::verify($this->eventDispatcher, Phake::times(2))->dispatch(Phake::anyParameters());
    }
}
