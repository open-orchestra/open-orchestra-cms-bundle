<?php

namespace OpenOrchestra\GroupBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\GroupBundle\EventSubscriber\SoftDeleteGroupSubscriber;
use OpenOrchestra\ModelInterface\SiteEvents;
use Phake;

/**
 * Class SoftDeleteGroupSubscriberTest
 */
class SoftDeleteGroupSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var SoftDeleteGroupSubscriber
     */
    protected $subscriber;
    protected $site;
    protected $event;
    protected $groupRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getSite()->thenReturn($this->site);

        $this->groupRepository = Phake::mock('OpenOrchestra\Backoffice\Repository\GroupRepositoryInterface');

        $this->subscriber = new SoftDeleteGroupSubscriber($this->groupRepository);
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
        $this->assertArrayHasKey(SiteEvents::SITE_DELETE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test soft delete group
     */
    public function testSoftDeleteGroup()
    {
        $this->subscriber->softDeleteGroup($this->event);
        \Phake::verify($this->groupRepository)->softDeleteGroupsBySite($this->site);
    }
}
