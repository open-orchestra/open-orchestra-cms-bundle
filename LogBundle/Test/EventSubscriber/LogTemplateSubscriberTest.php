<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogTemplateSubscriber;
use PHPOrchestra\ModelInterface\TemplateEvents;

/**
 * Class LogTemplateSubscriber
 */
class LogTemplateSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogTemplateSubscriber
     */
    protected $subscriber;

    protected $logger;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogTemplateSubscriber($this->logger);
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
            array(TemplateEvents::TEMPLATE_CREATE),
            array(TemplateEvents::TEMPLATE_DELETE),
            array(TemplateEvents::TEMPLATE_UPDATE),
        );
    }
}
