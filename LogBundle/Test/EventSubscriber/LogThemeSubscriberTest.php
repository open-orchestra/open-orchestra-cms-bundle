<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogThemeSubscriber;
use PHPOrchestra\ModelInterface\ThemeEvents;

/**
 * Class LogThemeSubscriberTest
 */
class LogThemeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogThemeSubscriber
     */
    protected $subscriber;

    protected $logger;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogThemeSubscriber($this->logger);
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
            array(ThemeEvents::THEME_CREATE),
            array(ThemeEvents::THEME_DELETE),
            array(ThemeEvents::THEME_UPDATE),
        );
    }
}
