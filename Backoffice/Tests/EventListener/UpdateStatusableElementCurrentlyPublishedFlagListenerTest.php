<?php

namespace OpenOrchestra\Backoffice\Tests\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\EventListener\UpdateStatusableElementCurrentlyPublishedFlagListener;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Repository\StatusableRepositoryInterface;
use Phake;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Test UpdateStatusableElementCurrentlyPublishedFlagListenerTest
 */
class UpdateStatusableElementCurrentlyPublishedFlagListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateStatusableElementCurrentlyPublishedFlagListener
     */
    protected $subscriber;

    protected $repository;
    protected $objectManager;
    protected $event;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->repository = Phake::mock(StatusableRepositoryInterface::CLASS);
        Phake::when($this->repository)->findAllCurrentlyPublishedByElementId(Phake::anyParameters())->thenReturn(array());

        $this->objectManager = Phake::mock(ObjectManager::CLASS);
        $this->event = Phake::mock(NodeEvent::CLASS);

        $this->subscriber = new UpdateStatusableElementCurrentlyPublishedFlagListener($this->repository, $this->objectManager);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertNotInstanceOf(EventSubscriberInterface::CLASS, $this->subscriber);
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
        $node = Phake::mock(StatusableInterface::CLASS);
        Phake::when($node)->getStatus()->thenReturn($status);
        Phake::when($node)->getVersion()->thenReturn($nodeVersion);
        Phake::when($node)->isCurrentlyPublished()->thenReturn($publishedFlag);
        Phake::when($this->event)->getStatusableElement()->thenReturn($node);

        $lastPublishedNode = Phake::mock(StatusableInterface::CLASS);
        Phake::when($lastPublishedNode)->getVersion()->thenReturn($lastPublishedNodeVersion);
        Phake::when($this->repository)->findOneCurrentlyPublished(Phake::anyParameters())->thenReturn($lastPublishedNode);
        Phake::when($this->repository)->findPublishedInLastVersionWithoutFlag(Phake::anyParameters())->thenReturn($lastPublishedNode);

        $previousStatus = Phake::mock(StatusInterface::CLASS);
        Phake::when($previousStatus)->isPublished()->thenReturn($previousStatusPublished);
        Phake::when($this->event)->getPreviousStatus()->thenReturn($previousStatus);

        $wrongPublishedNode = Phake::mock(StatusableInterface::CLASS);
        Phake::when($this->repository)->findAllCurrentlyPublishedByElementId(Phake::anyParameters())->thenReturn(array($wrongPublishedNode, $wrongPublishedNode));

        $this->subscriber->updateFlag($this->event);

        Phake::verify($this->repository, Phake::times($repositoryCall))->findOneCurrentlyPublished(Phake::anyParameters());
        Phake::verify($this->repository, Phake::times($lastPublishedCall))->findPublishedInLastVersionWithoutFlag(Phake::anyParameters());
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
            'no element published' => array(0, 0, 0, false, 4, false, 2, false),
            'publish newer element' => array(1, 1, 0, true, 2, false, 1, false),
            'publish older element' => array(1, 0, 0, true, 1, false, 2, false),
            'unpublish older element' => array(0, 0, 0, false, 1, false, 2, true),
            'unpublish last element' => array(0, 0, 1, false, 2, true, 1, true),
        );
    }

    /**
     * Test update node with no previous published version
     */
    public function testUpdateFlagWithNoPreviousPublishedNode()
    {
        $status = Phake::mock(StatusInterface::CLASS);
        Phake::when($status)->isPublished()->thenReturn(true);
        $node = Phake::mock(StatusableInterface::CLASS);
        Phake::when($node)->getStatus()->thenReturn($status);
        Phake::when($node)->getVersion()->thenReturn(1);
        Phake::when($node)->isCurrentlyPublished()->thenReturn(false);
        Phake::when($this->event)->getStatusableElement()->thenReturn($node);

        $previousStatus = Phake::mock(StatusInterface::CLASS);
        Phake::when($previousStatus)->isPublished()->thenReturn(false);
        Phake::when($this->event)->getPreviousStatus()->thenReturn($previousStatus);

        $this->subscriber->updateFlag($this->event);

        Phake::verify($this->repository)->findOneCurrentlyPublished(Phake::anyParameters());
        Phake::verify($node)->setCurrentlyPublished(true);
        Phake::verify($this->objectManager)->flush($node);
    }

    /**
     * Test unpublish with no other published elements
     */
    public function testUpdateFlagWhenUnpblishNodeAndNoOtherPublishedNode()
    {
        $status = Phake::mock(StatusInterface::CLASS);
        Phake::when($status)->isPublished()->thenReturn(false);
        $node = Phake::mock(StatusableInterface::CLASS);
        Phake::when($node)->getStatus()->thenReturn($status);
        Phake::when($node)->getVersion()->thenReturn(2);
        Phake::when($node)->isCurrentlyPublished()->thenReturn(true);
        Phake::when($this->event)->getStatusableElement()->thenReturn($node);

        $previousStatus = Phake::mock(StatusInterface::CLASS);
        Phake::when($previousStatus)->isPublished()->thenReturn(true);
        Phake::when($this->event)->getPreviousStatus()->thenReturn($previousStatus);

        $wrongPublishedNode = Phake::mock(StatusableInterface::CLASS);
        Phake::when($this->repository)->findAllCurrentlyPublishedByElementId(Phake::anyParameters())->thenReturn(array($wrongPublishedNode, $wrongPublishedNode));

        $this->subscriber->updateFlag($this->event);

        Phake::verify($this->repository, Phake::never())->findOneCurrentlyPublished(Phake::anyParameters());
        Phake::verify($this->repository)->findPublishedInLastVersionWithoutFlag(Phake::anyParameters());
        Phake::verify($node)->setCurrentlyPublished(false);
        Phake::verify($this->objectManager)->flush($node);
    }
}
