<?php

namespace OpenOrchestra\UserAdminBundle\Tests\EventSubscriber;

use FOS\UserBundle\FOSUserEvents;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\UserAdminBundle\EventSubscriber\ResetPasswordSubscriber;
use Phake;

/**
 * Class ResetPasswordSubscriberTest
 */
class ResetPasswordSubscriberTest extends AbstractBaseTestCase
{
    protected $event;
    protected $router;
    protected $subscriber;

    /**
     * Set up common test part
     */
    public function setUp()
    {
        $this->event = Phake::mock('FOS\UserBundle\Event\FormEvent');
        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');

        $this->subscriber = new ResetPasswordSubscriber($this->router);
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
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(FOSUserEvents::RESETTING_RESET_SUCCESS, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test on resetting reset success
     */
    public function testOnResettingResetSuccess()
    {
        $this->subscriber->onResettingResetSuccess($this->event);
        Phake::verify($this->event)->setResponse(Phake::anyParameters());
    }
}
