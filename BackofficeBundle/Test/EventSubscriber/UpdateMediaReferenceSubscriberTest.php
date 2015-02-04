<?php

namespace PHPOrchestra\BackofficeBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\BackofficeBundle\EventSubscriber\UpdateMediaReferenceSubscriber;
use PHPOrchestra\ModelInterface\StatusEvents;

/**
 * Test UpdateMediaReferenceSubscriberTest
 */
class UpdateMediaReferenceSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateMediaReferenceSubscriber
     */
    protected $subscriber;

    protected $event;
    protected $media;
    protected $status;
    protected $mediaRepository;
    protected $statusableElement;
    protected $extractReferenceManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->extractReferenceManager = Phake::mock('PHPOrchestra\BackofficeBundle\StrategyManager\ExtractReferenceManager');

        $this->status = Phake::mock('PHPOrchestra\ModelInterface\Model\StatusInterface');
        $this->statusableElement = Phake::mock('PHPOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($this->statusableElement)->getStatus()->thenReturn($this->status);
        $this->event = Phake::mock('PHPOrchestra\ModelInterface\Event\StatusableEvent');
        Phake::when($this->event)->getStatusableElement()->thenReturn($this->statusableElement);

        $this->media = Phake::mock('PHPOrchestra\Media\Model\MediaInterface');
        $this->mediaRepository = Phake::mock('PHPOrchestra\Media\Repository\MediaRepositoryInterface');
        Phake::when($this->mediaRepository)->find(Phake::anyParameters())->thenReturn($this->media);

        $this->subscriber = new UpdateMediaReferenceSubscriber($this->extractReferenceManager, $this->mediaRepository);
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
    public function testSubscrbedEvents()
    {
        $this->assertArrayHasKey(StatusEvents::STATUS_CHANGE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param bool   $isPublished
     * @param string $methodToCall
     *
     * @dataProvider provideStatusAndMethodToCall
     */
    public function testUpdateMediaReference($isPublished, $methodToCall)
    {
        Phake::when($this->status)->isPublished()->thenReturn($isPublished);
        Phake::when($this->extractReferenceManager)->extractReference(Phake::anyParameters())->thenReturn(array(
            'foo' => array('node-nodeId-0', 'node-nodeId-1'),
            'bar' => array('node-nodeId-1'),
        ));

        $this->subscriber->updateMediaReference($this->event);

        Phake::verify($this->extractReferenceManager)->extractReference($this->statusableElement);
        Phake::verify($this->media, Phake::times(2))->$methodToCall('node-nodeId-1');
        Phake::verify($this->media)->$methodToCall('node-nodeId-0');
    }

    /**
     * @return array
     */
    public function provideStatusAndMethodToCall()
    {
        return array(
            array(true, 'addUsageReference'),
            array(false, 'removeUsageReference'),
        );
    }
}
