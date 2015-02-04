<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogRoleSubscriber;
use PHPOrchestra\ModelInterface\RoleEvents;

/**
 * Class LogRoleSubscriberTest
 */
class LogRoleSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogRoleSubscriber
     */
    protected $subscriber;

    protected $logger;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogRoleSubscriber($this->logger);
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
            array(RoleEvents::ROLE_CREATE),
            array(RoleEvents::ROLE_DELETE),
            array(RoleEvents::ROLE_UPDATE),
        );
    }
}
