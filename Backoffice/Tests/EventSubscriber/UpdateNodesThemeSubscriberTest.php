<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\UpdateNodesThemeSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\ThemeEvents;
use Phake;

/**
 * Test UpdateNodesThemeSubscriberTest
 */
class UpdateNodesThemeSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var UpdateNodesThemeSubscriber
     */
    protected $subscriber;
    protected $objectManager;
    protected $nodeRepository;
    protected $event;
    protected $theme;
    protected $oldTheme;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->event = Phake::mock('OpenOrchestra\ModelInterface\Event\ThemeEvent');

        $this->theme = Phake::mock('OpenOrchestra\ModelInterface\Model\ThemeInterface');
        $this->oldTheme = Phake::mock('OpenOrchestra\ModelInterface\Model\ThemeInterface');
        Phake::when($this->event)->getTheme()->thenReturn($this->theme);
        Phake::when($this->event)->getOldTheme()->thenReturn($this->oldTheme);

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        Phake::when($this->nodeRepository)->findByTheme(Phake::anyParameters())->thenReturn(array($this->node));

        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->subscriber = new UpdateNodesThemeSubscriber($this->nodeRepository, $this->objectManager);
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
    public function testSubscribedEvents()
    {
        $this->assertArrayHasKey(ThemeEvents::THEME_UPDATE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test Update Theme
     */
    public function testUpdateTheme()
    {
        $newTheme = 'newTheme';
        Phake::when($this->oldTheme)->getName()->thenReturn('oldName');
        Phake::when($this->theme)->getName()->thenReturn($newTheme);

        $this->subscriber->updateTheme($this->event);

        Phake::verify($this->node)->setTheme($newTheme);
        Phake::verify($this->objectManager)->flush();
    }

    /**
     * Test update theme with same name
     */
    public function testUpdateThemeWithSameName()
    {
        $name = 'sameName';
        Phake::when($this->oldTheme)->getName()->thenReturn($name);
        Phake::when($this->theme)->getName()->thenReturn($name);

        $this->subscriber->updateTheme($this->event);

        Phake::verify($this->node, Phake::never())->setTheme(Phake::anyParameters());
        Phake::verify($this->objectManager, Phake::never())->flush();
    }
}
