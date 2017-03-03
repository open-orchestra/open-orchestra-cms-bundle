<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\DeleteContentSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\ContentEvents;
use phake;

/**
 * Class DeleteContentSubscriberTest
 */
class DeleteContentSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var DeleteContentSubscriber
     */
    protected $subscriber;

    protected $objectManager;
    protected $contentEvent;
    protected $contentId = 'fakeId';
    protected $siteId = 'siteId';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\ContentDeleteEvent');
        Phake::when($this->contentEvent)->getEntityId()->thenReturn($this->contentId);
        Phake::when($this->contentEvent)->getSiteId()->thenReturn($this->siteId);
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $trashItemClass = 'OpenOrchestra\ModelBundle\Document\TrashItem';
        $this->subscriber = new DeleteContentSubscriber($this->objectManager, $trashItemClass);
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
        $this->assertArrayHasKey(ContentEvents::CONTENT_DELETE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test add content in TrashCan
     */
    public function testAddContentTrashCan()
    {
        $this->subscriber->addContentTrashCan($this->contentEvent);

        Phake::verify($this->objectManager)->persist(phake::anyParameters());
        Phake::verify($this->objectManager)->flush(phake::anyParameters());
    }
}
