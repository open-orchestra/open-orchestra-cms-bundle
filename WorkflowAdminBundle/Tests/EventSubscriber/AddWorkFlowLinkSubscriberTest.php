<?php

namespace OpenOrchestra\WorkflowAdminBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\WorkflowAdminBundle\EventSubscriber\AddWorkFlowLinkSubscriber;
use OpenOrchestra\UserAdminBundle\UserFacadeEvents;
use Phake;

/**
 * Class AddWorkFlowLinkSubscriberTest
 */
class AddWorkFlowLinkSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var AddWorkFlowLinkSubscriber
     */
    protected $subscriber;

    protected $router;
    protected $userFacadeEvent;
    protected $userFacade;
    protected $fakeRoot = 'fakeRoot';
    protected $user;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->router = Phake::mock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->userFacadeEvent = Phake::mock('OpenOrchestra\UserAdminBundle\Event\UserFacadeEvent');
        $this->userFacade = Phake::mock('OpenOrchestra\UserAdminBundle\Facade\UserFacade');
        $this->user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');

        Phake::when($this->userFacadeEvent)->getUserFacade()->thenReturn($this->userFacade);
        Phake::when($this->userFacadeEvent)->getUser()->thenReturn($this->user);
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn($this->fakeRoot);

        $this->subscriber = new AddWorkFlowLinkSubscriber($this->router);
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
        $this->assertArrayHasKey(UserFacadeEvents::POST_USER_TRANSFORMATION, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test postUserTransformation
     */
    /**
     * @param int  $countAddLink
     * @param bool $isSuperAdmin
     *
     * @dataProvider provideUserIsSuperAdmin
     */
    public function testPostUserTransformation($countAddLink, $isSuperAdmin)
    {
        Phake::when($this->user)->isSuperAdmin()->thenReturn($isSuperAdmin);
        $this->subscriber->postUserTransformation($this->userFacadeEvent);
        Phake::verify($this->userFacade, Phake::times($countAddLink))->addLink('_self_panel_workflow_right', $this->fakeRoot);
    }

    /**
     * @return array
     */
    public function provideUserIsSuperAdmin()
    {
        return array(
            array(0, true),
            array(1, false),
        );
    }
}
