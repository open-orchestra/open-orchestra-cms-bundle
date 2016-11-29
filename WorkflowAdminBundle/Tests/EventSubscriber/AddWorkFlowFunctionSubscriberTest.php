<?php

namespace OpenOrchestra\WorkflowAdminBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\WorkflowAdminBundle\EventSubscriber\AddWorkFlowFunctionSubscriber;
use OpenOrchestra\Workflow\WorkflowRightEvents;
use Doctrine\Common\Collections\ArrayCollection;

use Phake;

/**
 * Class AddWorkFlowFunctionSubscriberTest
 */
class AddWorkFlowFunctionSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var AddWorkFlowFunctionSubscriber
     */
    protected $subscriber;

    protected $user;
    protected $userRepository;
    protected $documentManager;
    protected $workflowRightEvent;
    protected $workflowRight;
    protected $authorizations;
    protected $authorization;
    protected $workflowFunctions;
    protected $workflowFunction;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->user = Phake::mock('OpenOrchestra\UserBundle\Document\User');
        $this->userRepository = Phake::mock('OpenOrchestra\UserBundle\Repository\UserRepository');
        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $this->workflowFunction = Phake::mock('OpenOrchestra\Workflow\Model\WorkflowFunctionInterface');
        $this->authorization = Phake::mock('OpenOrchestra\Workflow\Model\AuthorizationInterface');
        $this->workflowRight = Phake::mock('OpenOrchestra\Workflow\Model\WorkflowRightInterface');
        $this->workflowRightEvent = Phake::mock('OpenOrchestra\Workflow\Event\WorkflowRightEvent');

        $this->authorizations = new ArrayCollection();
        $this->workflowFunctions = new ArrayCollection();

        $this->authorizations->add($this->authorization);
        $this->workflowFunctions->add($this->workflowFunction);

        Phake::when($this->authorization)->getWorkflowFunctions()->thenReturn($this->workflowFunctions);
        Phake::when($this->workflowRight)->getAuthorizations()->thenReturn($this->authorizations);
        Phake::when($this->workflowRight)->getUserId()->thenReturn('fakeUserId');
        Phake::when($this->workflowRightEvent)->getWorkflowRight()->thenReturn($this->workflowRight);
        Phake::when($this->userRepository)->find(Phake::anyParameters())->thenReturn($this->user);

        $this->subscriber = new AddWorkFlowFunctionSubscriber($this->userRepository, $this->documentManager);
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
        $this->assertArrayHasKey(WorkflowRightEvents::WORKFLOWRIGHT_UPDATE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test postUserTransformation
     *
     * @param array $existingRoles
     * @param array $newRoles
     * @param array $expectingRoles
     *
     * @dataProvider getExistingAndNewRoles
     */
    public function testPostUserUpdate($existingRoles, $newRoles, $expectingRoles)
    {
        $roles = new ArrayCollection();

        foreach ($newRoles as $newRole) {
            $role = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');
            Phake::when($role)->getName()->thenReturn($newRole);
            $roles->add($role);
        }

        Phake::when($this->workflowFunction)->getRoles()->thenReturn($roles);
        Phake::when($this->user)->getRoles()->thenReturn($existingRoles);

        $this->subscriber->postUserUpdate($this->workflowRightEvent);

        foreach ($expectingRoles as $expectingRole) {
            Phake::verify($this->user)->addRole($expectingRole);
        }
    }

    /**
     * @return array
     */
    public function getExistingAndNewRoles()
    {
        return array(
            array(
                array(),
                array('foo', 'bar'),
                array('foo', 'bar')
            ),
            array(
                array('foo', 'bar'),
                array('foo', 'bar'),
                array()
            ),
            array(
                array(),
                array('foo', 'foo'),
                array('foo')
            ),
        );
    }
}
