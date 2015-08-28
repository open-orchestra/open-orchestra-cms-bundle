<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use OpenOrchestra\BackofficeBundle\EventSubscriber\DeleteContentSubscriber;
use OpenOrchestra\ModelInterface\ContentEvents;
use phake;

/**
 * Class DeleteContentSubscriberTest
 */
class DeleteContentSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DeleteContentSubscriber
     */
    protected $subscriber;

    protected $objectManager;
    protected $contentEvent;
    protected $content;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $this->contentEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\ContentEvent');
        Phake::when($this->contentEvent)->getContent()->thenReturn($this->content);
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
