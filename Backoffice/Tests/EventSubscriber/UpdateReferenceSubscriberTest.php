<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\UpdateReferenceSubscriber;
use OpenOrchestra\Backoffice\Reference\ReferenceManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;
use OpenOrchestra\ModelInterface\Event\BlockEvent;
use OpenOrchestra\ModelInterface\Event\TrashcanEvent;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use OpenOrchestra\ModelInterface\Model\SoftDeleteableInterface;
use OpenOrchestra\ModelInterface\TrashcanEvents;
use Phake;
use OpenOrchestra\ModelInterface\BlockEvents;

/**
 * Test UpdateReferenceSubscriberTest
 */
class UpdateReferenceSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var UpdateReferenceSubscriber
     */
    protected $subscriber;

    protected $referenceManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->referenceManager = Phake::mock(ReferenceManager::class);
        $this->subscriber = new UpdateReferenceSubscriber($this->referenceManager);

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
        $this->assertArrayHasKey(BlockEvents::POST_BLOCK_CREATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(BlockEvents::POST_BLOCK_UPDATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(BlockEvents::POST_BLOCK_DELETE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(ContentEvents::CONTENT_UPDATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(ContentEvents::CONTENT_CREATION, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(ContentEvents::CONTENT_DUPLICATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(ContentTypeEvents::CONTENT_TYPE_CREATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(ContentTypeEvents::CONTENT_TYPE_UPDATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(TrashcanEvents::TRASHCAN_REMOVE_ENTITY, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test update reference to node
     */
    public function testUpdateReferencesToBlock()
    {
        $block = Phake::mock(ReadBlockInterface::class);
        $blockEvent = Phake::mock(BlockEvent::class);
        Phake::when($blockEvent)->getBlock()->thenReturn($block);

        $this->subscriber->updateReferencesToBlock($blockEvent);

        Phake::verify($this->referenceManager)->updateReferencesToEntity($block);
    }

    /**
     * Test update reference to content
     */
    public function testUpdateReferencesToContent()
    {
        $content = Phake::mock(ContentInterface::class);
        $contentEvent = Phake::mock(ContentEvent::class);
        Phake::when($contentEvent)->getContent()->thenReturn($content);

        $this->subscriber->updateReferencesToContent($contentEvent);

        Phake::verify($this->referenceManager)->updateReferencesToEntity($content);
    }

    /**
     * Test update reference to content type
     */
    public function testUpdateReferencesToContentType()
    {
        $contentType = Phake::mock(ContentTypeInterface::class);
        $contentTypeEvent = Phake::mock(ContentTypeEvent::class);
        Phake::when($contentTypeEvent)->getContentType()->thenReturn($contentType);

        $this->subscriber->updateReferencesToContentType($contentTypeEvent);

        Phake::verify($this->referenceManager)->updateReferencesToEntity($contentType);
    }

    /**
     * Test remove reference to entity
     */
    public function testRemoveReferencesToEntity()
    {
        $deletedEntity = Phake::mock(SoftDeleteableInterface::class);
        $trashcanEvent = Phake::mock(TrashcanEvent::class);
        Phake::when($trashcanEvent)->getDeletedEntity()->thenReturn($deletedEntity);

        $this->subscriber->removeReferencesToEntity($trashcanEvent);

        Phake::verify($this->referenceManager)->removeReferencesToEntity($deletedEntity);
    }

    /**
     * Test remove reference to entity
     */
    public function testRemoveReferencesToBlock()
    {
        $block = Phake::mock(ReadBlockInterface::class);
        $blockEvent = Phake::mock(BlockEvent::class);
        Phake::when($blockEvent)->getBlock()->thenReturn($block);

        $this->subscriber->removeReferencesToBlock($blockEvent);

        Phake::verify($this->referenceManager)->removeReferencesToEntity($block);
    }
}
