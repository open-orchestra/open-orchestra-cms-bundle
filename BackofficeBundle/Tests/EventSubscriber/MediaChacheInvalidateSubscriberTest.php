<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use Phake;
use OpenOrchestra\MediaBundle\EventSubscriber\MediaChacheInvalidateSubscriber;
use OpenOrchestra\Media\Model\MediaInterface;
use OpenOrchestra\Media\MediaEvents;

/**
 * Class MediaChacheInvalidateSubscriberTest
 */
class MediaChacheInvalidateSubscriberTest extends \PHPUnit_Framework_TestCase
{
    protected $tagManager;
    protected $cacheableManager;
    protected $subscriber;
    protected $event;
    protected $media;
    protected $mediaId = 'mediaId';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->tagManager = Phake::mock('OpenOrchestra\BaseBundle\Manager\TagManager');

        $this->cacheableManager = Phake::mock('OpenOrchestra\DisplayBundle\Manager\CacheableManager');

        $this->subscriber = new MediaChacheInvalidateSubscriber($this->cacheableManager, $this->tagManager);

        $this->media = Phake::mock('OpenOrchestra\Media\Model\MediaInterface');
        Phake::when($this->media)->getId()->thenReturn($this->mediaId);

        $this->event = Phake::mock('OpenOrchestra\Media\Event\MediaEvent');
        Phake::when($this->event)->getMedia()->thenReturn($this->media);
    }

    /**
     * test instance
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
        $this->assertArrayHasKey(MediaEvents::MEDIA_CROP, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(MediaEvents::MEDIA_DELETE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test methodes existance
     */
    public function testMethodExists()
    {
        $this->assertTrue(method_exists($this->subscriber, 'cropMedia'));
        $this->assertTrue(method_exists($this->subscriber, 'deleteMedia'));
    }

    /**
     * Test cropMedia
     */
    public function testCropMedia()
    {
        $this->subscriber->cropMedia($this->event);

        Phake::verify($this->cacheableManager, Phake::times(1))->invalidateTags(Phake::anyParameters());
    }

    /**
     * Test deleteMedia
     */
    public function testDeleteMedia()
    {
        $this->subscriber->deleteMedia($this->event);

        Phake::verify($this->cacheableManager, Phake::times(1))->invalidateTags(Phake::anyParameters());
    }
}
