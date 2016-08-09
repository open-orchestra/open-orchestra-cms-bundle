<?php

namespace OpenOrchestra\UserAdminBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\GroupBundle\GroupFacadeEvents;
use OpenOrchestra\UserAdminBundle\EventSubscriber\GroupUserListTransformSubscriber;
use Phake;

/**
 * Class GroupUserListTransformSubscriber
 */
abstract class GroupUserListTransformSubscriberTest extends AbstractBaseTestCase
{
    protected $router;
    protected $authorizationChecker;
    protected $event;
    protected $facade;
    protected $groupId = 'fakeId';

    /** @var GroupUserListTransformSubscriber */
    protected $subscriber;

    /**
     * Set up common test part
     */
    public function setUp()
    {
        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');

        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($this->group)->getId()->thenReturn($this->groupId);

        $this->event = Phake::mock('OpenOrchestra\GroupBundle\Event\GroupFacadeEvent');
        Phake::when($this->event)->getGroup()->thenReturn($group);
        Phake::when($this->event)->getFacade()->thenReturn($this->facade);

        $this->subscriber = new GroupUserListTransformSubscriber($this->router, $this->authorizationChecker);
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
        $this->assertArrayHasKey(GroupFacadeEvents::POST_GROUP_TRANSFORMATION, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param bool $isGranted
     * @param int  $nbAddLink
     *
     * @dataProvider provideAuthorizationAndAddLink
     */
    public function testPostGroupTransform($isGranted, $nbAddLink)
    {

        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn($isGranted);

        $this->subscriber->postGroupTransform($this->event);

        Phake::verify($this->facade, Phake::times($nbAddLink))->addLink(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideAuthorizationAndAddLink()
    {
        return array(
          array(true, 1),
          array(false, 0)
        );
    }
}
