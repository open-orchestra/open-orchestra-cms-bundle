<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\UpdateEmbeddedStatusSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\StatusEvents;
use Phake;

/**
 * Test UpdateEmbeddedStatusSubscriberTest
 */
class UpdateEmbeddedStatusSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var UpdateEmbeddedStatusSubscriber
     */
    protected $subscriber;
    protected $event;
    protected $status;
    protected $statusableRepository1;
    protected $statusableRepository2;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');

        $this->event = Phake::mock('OpenOrchestra\ModelInterface\Event\StatusEvent');
        Phake::when($this->event)->getStatus()->thenReturn($this->status);
        $this->statusableRepository1 = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusableContainerRepositoryInterface');
        $this->statusableRepository2 = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusableContainerRepositoryInterface');

        $this->subscriber = new UpdateEmbeddedStatusSubscriber(
            array(
                $this->statusableRepository1,
                $this->statusableRepository2
            )
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
        $this->assertArrayHasKey(StatusEvents::STATUS_UPDATE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * test constructor
     */
    public function testConstructor()
    {
        $siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        $this->expectException('\InvalidArgumentException');
        new UpdateEmbeddedStatusSubscriber(array($siteRepository));
    }

    /**
     * test updateEmbeddedStatus
     */
    public function testUpdateEmbeddedStatus()
    {
        $this->subscriber->updateEmbeddedStatus($this->event);
        Phake::verify($this->statusableRepository1)->updateEmbeddedStatus($this->status);
        Phake::verify($this->statusableRepository2)->updateEmbeddedStatus($this->status);
    }
}
