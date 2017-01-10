<?php

namespace OpenOrchestra\GroupBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\GroupBundle\EventSubscriber\GroupPerimeterSubscriber;
use Phake;
use Symfony\Component\Form\FormEvents;

/**
 * Class GroupPerimeterSubscriberTest
 */
class GroupPerimeterSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var GroupPerimeterSubscriber
     */
    protected $subscriber;
    protected $event;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');

        $this->subscriber = new GroupPerimeterSubscriber();
    }

    /**
     * Test instance
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
        $this->assertArrayHasKey(FormEvents::POST_SUBMIT, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test postSubmit
     */
    public function testPostSubmit()
    {
        $perimeter = Phake::mock('OpenOrchestra\Backoffice\Model\PerimeterInterface');
        Phake::when($perimeter)->getItems()->thenReturn(array());
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getSiteId()->thenReturn('fakeSited');
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group)->hasRole(Phake::anyParameters())->thenReturn(true);
        Phake::when($group)->getSite()->thenReturn($site);
        Phake::when($group)->getPerimeter(Phake::anyParameters())->thenReturn($perimeter);
        Phake::when($this->event)->getData()->thenReturn($group);

        $this->subscriber->postSubmit($this->event);

        Phake::verify($group)->addPerimeter(Phake::anyParameters());
    }
}
