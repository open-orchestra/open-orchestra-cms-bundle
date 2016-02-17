<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\DeleteNodeSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\NodeEvents;
use phake;

/**
 * Class DeleteNodeSubscriberTest
 */
class DeleteNodeSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var DeleteNodeSubscriber
     */
    protected $subscriber;

    protected $objectManager;
    protected $nodeEvent;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $this->nodeEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\NodeEvent');
        Phake::when($this->nodeEvent)->getNode()->thenReturn($this->node);
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $trashItemClass = 'OpenOrchestra\ModelBundle\Document\TrashItem';
        $this->subscriber = new DeleteNodeSubscriber($this->objectManager, $trashItemClass);
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
        $this->assertArrayHasKey(NodeEvents::NODE_DELETE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test add node in TrashCan
     */
    public function testAddNodeTrashCan()
    {
        $this->subscriber->addNodeTrashCan($this->nodeEvent);

        Phake::verify($this->objectManager)->persist(phake::anyParameters());
        Phake::verify($this->objectManager)->flush(phake::anyParameters());
    }
}
