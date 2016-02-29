<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\EventSubscriber\UpdateNodeCurrentlyPublishedFlagSubscriber;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Phake;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Test UpdateNodeCurrentlyPublishedFlagSubscriberTest
 */
class UpdateNodeCurrentlyPublishedFlagSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateNodeCurrentlyPublishedFlagSubscriber
     */
    protected $subscriber;

    protected $nodeRepository;
    protected $objectManager;
    protected $event;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock(NodeRepositoryInterface::CLASS);
        Phake::when($this->nodeRepository)->findAllCurrentlyPublishedByNode(Phake::anyParameters())->thenReturn(array());

        $this->objectManager = Phake::mock(ObjectManager::CLASS);
        $this->event = Phake::mock(NodeEvent::CLASS);

        $this->subscriber = new UpdateNodeCurrentlyPublishedFlagSubscriber($this->nodeRepository, $this->objectManager);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::CLASS, $this->subscriber);
    }

    /**
     * Test subscribed events
     */
    public function testSubscribedEvent()
    {
        $this->assertSame(array(
            NodeEvents::NODE_CHANGE_STATUS => array('updateFlag', 100)),
            $this->subscriber->getSubscribedEvents()
        );
    }

    /**
     * @param $repositoryCall
     * @param $managerCall
     * @param $statusPublished
     * @param $nodeVersion
     * @param $publishedFlag
     * @param $lastPublishedNodeVersion
     * @param $previousStatusPublished
     *
     * @dataProvider provideUpdateFlagData
     */
    public function testUpdateFlag($repositoryCall, $managerCall, $lastPublishedCall, $statusPublished, $nodeVersion, $publishedFlag, $lastPublishedNodeVersion, $previousStatusPublished)
    {
        $status = Phake::mock(StatusInterface::CLASS);
        Phake::when($status)->isPublished()->thenReturn($statusPublished);
        $node = Phake::mock(NodeInterface::CLASS);
        Phake::when($node)->getStatus()->thenReturn($status);
        Phake::when($node)->getVersion()->thenReturn($nodeVersion);
        Phake::when($node)->isCurrentlyPublished()->thenReturn($publishedFlag);
        Phake::when($this->event)->getNode()->thenReturn($node);

        $lastPublishedNode = Phake::mock(NodeInterface::CLASS);
        Phake::when($lastPublishedNode)->getVersion()->thenReturn($lastPublishedNodeVersion);
        Phake::when($this->nodeRepository)->findOneCurrentlyPublished(Phake::anyParameters())->thenReturn($lastPublishedNode);
        Phake::when($this->nodeRepository)->findPublishedInLastVersionWithoutFlag(Phake::anyParameters())->thenReturn($lastPublishedNode);

        $previousStatus = Phake::mock(StatusInterface::CLASS);
        Phake::when($previousStatus)->isPublished()->thenReturn($previousStatusPublished);
        Phake::when($this->event)->getPreviousStatus()->thenReturn($previousStatus);

        $wrongPublishedNode = Phake::mock(NodeInterface::CLASS);
        Phake::when($this->nodeRepository)->findAllCurrentlyPublishedByNode(Phake::anyParameters())->thenReturn(array($wrongPublishedNode, $wrongPublishedNode));

        $this->subscriber->updateFlag($this->event);

        Phake::verify($this->nodeRepository, Phake::times($repositoryCall))->findOneCurrentlyPublished(Phake::anyParameters());
        Phake::verify($this->nodeRepository, Phake::times($lastPublishedCall))->findPublishedInLastVersionWithoutFlag(Phake::anyParameters());
        Phake::verify($this->objectManager, Phake::times($managerCall))->flush($node);
        Phake::verify($this->objectManager, Phake::times($lastPublishedCall))->flush($lastPublishedNode);
        if ($statusPublished && $nodeVersion >= $lastPublishedNodeVersion) {
            Phake::verify($wrongPublishedNode, Phake::times(2))->setCurrentlyPublished(false);
            Phake::verify($this->objectManager, Phake::times(2))->flush($wrongPublishedNode);
        }
    }

    /**
     * @return array
     */
    public function provideUpdateFlagData()
    {
        return array(
            'nothing published' => array(0, 0, 0, false, 4, false, 2, false),
            'publish newer node' => array(1, 1, 0, true, 2, false, 1, false),
            'publish older node' => array(1, 0, 0, true, 1, false, 2, false),
            'unpublish older node' => array(0, 0, 0, false, 1, false, 2, true),
            'unpublish last node' => array(0, 0, 1, false, 2, true, 1, true),
        );
    }

    /**
     * Test update node with no previous published version
     */
    public function testUpdateFlagWithNoPreviousPublishedNode()
    {
        $status = Phake::mock(StatusInterface::CLASS);
        Phake::when($status)->isPublished()->thenReturn(true);
        $node = Phake::mock(NodeInterface::CLASS);
        Phake::when($node)->getStatus()->thenReturn($status);
        Phake::when($node)->getVersion()->thenReturn(1);
        Phake::when($node)->isCurrentlyPublished()->thenReturn(false);
        Phake::when($this->event)->getNode()->thenReturn($node);

        $previousStatus = Phake::mock(StatusInterface::CLASS);
        Phake::when($previousStatus)->isPublished()->thenReturn(false);
        Phake::when($this->event)->getPreviousStatus()->thenReturn($previousStatus);

        $this->subscriber->updateFlag($this->event);

        Phake::verify($this->nodeRepository)->findOneCurrentlyPublished(Phake::anyParameters());
        Phake::verify($node)->setCurrentlyPublished(true);
        Phake::verify($this->objectManager)->flush($node);
    }

    public function testUpdateFlagWhenUnpblishNodeAndNoOtherPublishedNode()
    {
        $status = Phake::mock(StatusInterface::CLASS);
        Phake::when($status)->isPublished()->thenReturn(false);
        $node = Phake::mock(NodeInterface::CLASS);
        Phake::when($node)->getStatus()->thenReturn($status);
        Phake::when($node)->getVersion()->thenReturn(2);
        Phake::when($node)->isCurrentlyPublished()->thenReturn(true);
        Phake::when($this->event)->getNode()->thenReturn($node);

        $previousStatus = Phake::mock(StatusInterface::CLASS);
        Phake::when($previousStatus)->isPublished()->thenReturn(true);
        Phake::when($this->event)->getPreviousStatus()->thenReturn($previousStatus);

        $wrongPublishedNode = Phake::mock(NodeInterface::CLASS);
        Phake::when($this->nodeRepository)->findAllCurrentlyPublishedByNode(Phake::anyParameters())->thenReturn(array($wrongPublishedNode, $wrongPublishedNode));

        $this->subscriber->updateFlag($this->event);

        Phake::verify($this->nodeRepository, Phake::never())->findOneCurrentlyPublished(Phake::anyParameters());
        Phake::verify($this->nodeRepository)->findPublishedInLastVersionWithoutFlag(Phake::anyParameters());
        Phake::verify($node)->setCurrentlyPublished(false);
        Phake::verify($this->objectManager)->flush($node);
    }
}
