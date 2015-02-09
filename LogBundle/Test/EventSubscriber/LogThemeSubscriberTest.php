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

    protected $themeEvent;
    protected $logger;
    protected $theme;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->theme = Phake::mock('PHPOrchestra\ModelBundle\Document\Theme');
        $this->themeEvent = Phake::mock('PHPOrchestra\ModelInterface\Event\ThemeEvent');
        Phake::when($this->themeEvent)->getTheme()->thenReturn($this->theme);
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

    /**
     * Test themeCreate
     */
    public function testThemeCreate()
    {
        $this->subscriber->themeCreate($this->themeEvent);
        $this->eventTest('php_orchestra_log.theme.create');
    }

    /**
     * Test themeDelete
     */
    public function testThemeDelete()
    {
        $this->subscriber->themeDelete($this->themeEvent);
        $this->eventTest('php_orchestra_log.theme.delete');
    }

    /**
     * Test themeCreate
     */
    public function testThemeUpdate()
    {
        $this->subscriber->themeUpdate($this->themeEvent);
        $this->eventTest('php_orchestra_log.theme.update');
    }

    /**
     * Test the themeEvent
     *
     * @param string $message
     */
    public function eventTest($message)
    {
        Phake::verify($this->themeEvent)->getTheme();
        Phake::verify($this->logger)->info($message, array('theme_name' => $this->theme->getName()));
    }
}
