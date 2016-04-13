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
        );
    }

    /**
     * @param int  $countInvalidate
     * @param bool $isPublished
     *
     * @dataProvider provideCountInvalidateAndStatus
     */
    public function testContentChangeStatus($countInvalidate, $isPublished)
    {
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->isPublished()->thenReturn($isPublished);
        Phake::when($this->contentEvent)->getPreviousStatus()->thenReturn($status);
        $this->subscriber->contentChangeStatus($this->contentEvent);

        Phake::verify($this->cacheableManager, Phake::times($countInvalidate))->invalidateTags(array($this->contentIdTag));
    }

    /**
     * @return array
     */
    public function provideCountInvalidateAndStatus()
    {
        return array(
            "status not published status" => array(0, false),
            "status is published status" =>  array(1, true)
        );
    }

    /**
     * @param int  $countInvalidate
     * @param bool $isPublished
     *
     * @dataProvider provideCountInvalidateAndStatus
     */
    public function testDeleteContent($countInvalidate, $isPublished)
    {
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->isPublished()->thenReturn($isPublished);
        Phake::when($this->content)->getStatus()->thenReturn($status);
        $this->subscriber->deleteContentPublished($this->contentEvent);

        Phake::verify($this->cacheableManager, Phake::times($countInvalidate))->invalidateTags(array($this->contentIdTag));
    }
}
