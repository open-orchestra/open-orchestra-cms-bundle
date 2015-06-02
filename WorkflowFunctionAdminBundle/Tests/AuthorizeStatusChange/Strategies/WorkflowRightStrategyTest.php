<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Tests\AuthorizeStatusChange\Strategies;

use OpenOrchestra\WorkflowFunctionAdminBundle\AuthorizeStatusChange\Strategies\WorkflowRightStrategy;
use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use Phake;

/**
 * Class WorkflowRightStrategyTest
 */
class WorkflowRightStrategyTest extends \PHPUnit_Framework_TestCase
{
    protected $workflowRightStrategy;
    protected $authorizationChecker;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');

        $role = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');

        $roleRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface');
        Phake::when($roleRepository)->findOneByFromStatusAndToStatus(Phake::anyParameters())->thenReturn($role);

        $workflowFunctionRepository = Phake::mock('OpenOrchestra\WorkflowFunction\Repository\WorkflowFunctionRepositoryInterface');
        Phake::when($workflowFunctionRepository)->findByRole(Phake::anyParameters())->thenReturn(array());

        $this->workflowRightStrategy = new WorkflowRightStrategy($this->authorizationChecker, $roleRepository, $workflowFunctionRepository);
    }

    /**
     * @param StatusableEvent $event
     * @param bool            $granted
     * @param bool            $expectedResult
     *
     * @dataProvider provideStatusableEvent
     */
    public function testIsGranted(StatusableEvent $event, $granted, $expectedResult)
    {
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn($granted);
        $this->assertEquals($expectedResult, $this->workflowRightStrategy->isGranted($event));
    }

    /**
     * @return array
     */
    public function provideStatusableEvent()
    {
        $statusInterface0_0 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($statusInterface0_0)->getId()->thenReturn('fakeStatusId');

        $statusInterface0_1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($statusInterface0_1)->getId()->thenReturn('fakeStatusId');

        $statusableInterface0 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($statusableInterface0)->getStatus()->thenReturn($statusInterface0_0);

        $event0 = Phake::mock('OpenOrchestra\ModelInterface\Event\StatusableEvent');
        Phake::when($event0)->getStatusableElement()->thenReturn($statusableInterface0);
        Phake::when($event0)->getToStatus()->thenReturn($statusInterface0_1);


        $statusInterface1_0 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($statusInterface1_0)->getId()->thenReturn('fakeStatusId0');

        $statusInterface1_1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($statusInterface1_1)->getId()->thenReturn('fakeStatusId1');

        $statusableInterface1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($statusableInterface1)->getStatus()->thenReturn($statusInterface1_0);

        $event1 = Phake::mock('OpenOrchestra\ModelInterface\Event\StatusableEvent');
        Phake::when($event1)->getStatusableElement()->thenReturn($statusableInterface1);
        Phake::when($event1)->getToStatus()->thenReturn($statusInterface1_1);


        return array(
            array($event0, true, true),
            array($event1, false, false),
            array($event1, true, true),
        );

    }
}
