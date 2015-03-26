<?php

namespace OpenOrchestra\LogBundle\Tests\EventSubscriber;

use Phake;
use OpenOrchestra\LogBundle\EventSubscriber\LogThemeSubscriber;
use OpenOrchestra\ModelInterface\ThemeEvents;

/**
 * Class LogThemeSubscriberTest
 */
class LogThemeSubscriberTest extends LogAbstractSubscriberTest
{
    protected $themeEvent;
    protected $theme;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->theme = Phake::mock('OpenOrchestra\ModelBundle\Document\Theme');
        $this->themeEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\ThemeEvent');
        Phake::when($this->themeEvent)->getTheme()->thenReturn($this->theme);

        $this->subscriber = new LogThemeSubscriber($this->logger);
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
        $this->assertEventLogged('open_orchestra_log.theme.create', array(
            'theme_name' => $this->theme->getName()
        ));
    }

    /**
     * Test themeDelete
     */
    public function testThemeDelete()
    {
        $this->subscriber->themeDelete($this->themeEvent);
        $this->assertEventLogged('open_orchestra_log.theme.delete', array(
            'theme_name' => $this->theme->getName()
        ));
    }

    /**
     * Test themeCreate
     */
    public function testThemeUpdate()
    {
        $this->subscriber->themeUpdate($this->themeEvent);
        $this->assertEventLogged('open_orchestra_log.theme.update', array(
            'theme_name' => $this->theme->getName()
        ));
    }
}
