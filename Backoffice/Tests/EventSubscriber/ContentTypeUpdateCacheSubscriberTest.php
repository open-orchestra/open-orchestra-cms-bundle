<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\ContentTypeUpdateCacheSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\ContentTypeEvents;
use Phake;

/**
 * Class ContentTypeUpdateCacheSubscriberTest
 */
class ContentTypeUpdateCacheSubscriberTest extends AbstractBaseTestCase
{
    protected $cacheableManager;
    protected $tagManager;
    protected $contentTypeEvent;
    protected $contentType;
    protected $contentTypeId = 'contentTypeId';
    protected $contentTypeIdTag = 'contentTypeIdTag';
    /**
     * @var ContentTypeUpdateCacheSubscriber
     */
    protected $subscriber;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->cacheableManager = Phake::mock('OpenOrchestra\DisplayBundle\Manager\CacheableManager');
        $this->tagManager = Phake::mock('OpenOrchestra\BaseBundle\Manager\TagManager');
        Phake::when($this->tagManager)->formatContentTypeTag(Phake::anyParameters())->thenReturn($this->contentTypeIdTag);

        $this->contentType = Phake::mock('OpenOrchestra\ModelBundle\Document\ContentType');
        Phake::when($this->contentType)->getContentTypeId()->thenReturn($this->contentTypeId);

        $this->contentTypeEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\ContentTypeEvent');
        Phake::when($this->contentTypeEvent)->getContentType()->thenReturn($this->contentType);

        $this->subscriber = new ContentTypeUpdateCacheSubscriber($this->cacheableManager, $this->tagManager);
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
            array(ContentTypeEvents::CONTENT_TYPE_UPDATE),
            array(ContentTypeEvents::CONTENT_TYPE_DELETE),
        );
    }

    /**
     * Test invalidate tag content type
     */
    public function testInvalidateTagContentType()
    {
        $this->subscriber->invalidateTagContentType($this->contentTypeEvent);

        Phake::verify($this->cacheableManager)->invalidateTags(array($this->contentTypeIdTag));
    }
}
