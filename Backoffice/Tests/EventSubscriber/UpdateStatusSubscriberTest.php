<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\UpdateStatusSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\StatusEvents;
use Phake;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Test UpdateStatusSubscriberTest
 */
class UpdateStatusSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var UpdateStatusSubscriber
     */
    protected $subscriber;

    protected $event;
    protected $toStatus;
    protected $document;
    protected $authorizationChecker;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->document = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        $this->toStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->event = Phake::mock('OpenOrchestra\ModelInterface\Event\StatusableEvent');
        Phake::when($this->event)->getStatusableElement()->thenReturn($this->document);
        Phake::when($this->event)->getToStatus()->thenReturn($this->toStatus);
        $this->authorizationChecker = Phake::mock(AuthorizationCheckerInterface::class);

        $this->subscriber = new UpdateStatusSubscriber($this->authorizationChecker);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test subscribed events
     */
    public function testSubscribedEvents()
    {
        $this->assertArrayHasKey(StatusEvents::STATUS_CHANGE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test when ok
     */
    public function testUpdateStatusWhenOk()
    {
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(true);

        $this->subscriber->updateStatus($this->event);

        Phake::verify($this->document)->setStatus($this->toStatus);
    }

    /**
     * Test when not ok
     */
    public function testUpdateStatusWhenNotOk()
    {
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(false);

        $this->expectException('OpenOrchestra\Backoffice\Exception\StatusChangeNotGrantedException');
        $this->subscriber->updateStatus($this->event);

        Phake::verify($this->document, Phake::never())->setStatus(Phake::anyParameters());
    }
}
