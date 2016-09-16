<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\ContentUpdateCacheSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ModelInterface\ContentEvents;

/**
 * Class ContentUpdateCacheSubscriber
 */
class ContentUpdateCacheSubscriberTest extends AbstractBaseTestCase
{
    protected $cacheableManager;
    protected $tagManager;
    protected $contentEvent;
    protected $content;
    protected $contentId = 'contentId';
    protected $contentIdTag = 'contentIdTag';

    /** @var ContentUpdateCacheSubscriber */
    protected $subscriber;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->cacheableManager = Phake::mock('OpenOrchestra\DisplayBundle\Manager\CacheableManager');
        $this->tagManager = Phake::mock('OpenOrchestra\BaseBundle\Manager\TagManager');
        Phake::when($this->tagManager)->formatContentIdTag(Phake::anyParameters())->thenReturn($this->contentIdTag);

        $this->content = Phake::mock('OpenOrchestra\ModelBundle\Document\Content');
        Phake::when($this->content)->getContentId()->thenReturn($this->contentId);

        $this->contentEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\ContentEvent');
        Phake::when($this->contentEvent)->getContent()->thenReturn($this->content);

        $this->subscriber = new ContentUpdateCacheSubscriber($this->cacheableManager, $this->tagManager);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * @param string $eventName
     *
     * @dataProvider provideSubscribedEvent
     */
    public function testEventSubscribed($eventName)
    {
        $this->assertArrayHasKey($eventName, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(ContentEvents::CONTENT_CHANGE_STATUS),
            array(ContentEvents::CONTENT_DELETE),
            array(ContentEvents::CONTENT_UPDATE),
            array(ContentEvents::CONTENT_RESTORE),
        );
    }

    /**
     * @param int  $countInvalidate
     * @param bool $isPublishedPrevious
     *
     * @dataProvider provideCountInvalidateAndStatusOnChange
     */
    public function testContentChangeStatus($countInvalidate, $isPublishedPrevious)
    {
        $previousStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($previousStatus)->isPublished()->thenReturn($isPublishedPrevious);
        Phake::when($this->contentEvent)->getPreviousStatus()->thenReturn($previousStatus);

        $this->subscriber->invalidateCacheOnStatusChanged($this->contentEvent);

        Phake::verify($this->cacheableManager, Phake::times($countInvalidate))->invalidateTags(array($this->contentIdTag));
    }

    /**
     * @return array
     */
    public function provideCountInvalidateAndStatusOnChange()
    {
        return array(
            array(0, false),
            array(1, true),
        );
    }

    /**
     * @param int  $countInvalidate
     * @param bool $isPublished
     *
     * @dataProvider provideCountInvalidateAndStatusOnUpdate
     */
    public function testInvalidateCacheOnUpdatePublishedContent($countInvalidate, $isPublished)
    {
        $previousStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($previousStatus)->isPublished()->thenReturn($isPublished);
        Phake::when($this->contentEvent)->getPreviousStatus()->thenReturn($previousStatus);

        $this->subscriber->invalidateCacheOnUpdatePublishedContent($this->contentEvent);

        Phake::verify($this->cacheableManager, Phake::times($countInvalidate))->invalidateTags(array($this->contentIdTag));
    }

    /**
     * @return array
     */
    public function provideCountInvalidateAndStatusOnUpdate()
    {
        return array(
            array(1, true),
            array(0, false),
        );
    }

    /**
     * @param int  $countInvalidate
     * @param bool $isPublished
     *
     * @dataProvider provideCountInvalidateAndStatusOnDelete
     */
    public function testDeleteContent($countInvalidate, $isPublished)
    {
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->isPublished()->thenReturn($isPublished);
        Phake::when($this->content)->getStatus()->thenReturn($status);
        $this->subscriber->invalidateCacheOnDeletePublishedContent($this->contentEvent);

        Phake::verify($this->cacheableManager, Phake::times($countInvalidate))->invalidateTags(array($this->contentIdTag));
    }

    /**
     * @return array
     */
    public function provideCountInvalidateAndStatusOnDelete()
    {
        return array(
            array(0, false, true),
            array(1, true, true),
            array(0, false, false),
            array(1, true, false),
        );
    }
}
