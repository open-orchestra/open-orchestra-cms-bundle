<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Tests\AuthorizeStatusChange\Strategies;

use OpenOrchestra\WorkflowFunctionAdminBundle\AuthorizeStatusChange\Strategies\WorkflowRightStrategy;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
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
     * @param StatusableInterface $document
     * @param StatusInterface     $toStatus
     * @param bool                $granted
     * @param bool                $expectedResult
     *
     * @dataProvider provideStatusableEvent
     */
    public function testIsGranted(StatusableInterface $document, StatusInterface $toStatus, $granted, $expectedResult)
    {
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn($granted);
        $this->assertEquals($expectedResult, $this->workflowRightStrategy->isGranted($document, $toStatus));
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


        $statusInterface1_0 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($statusInterface1_0)->getId()->thenReturn('fakeStatusId0');

        $statusInterface1_1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($statusInterface1_1)->getId()->thenReturn('fakeStatusId1');

        $statusableInterface1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($statusableInterface1)->getStatus()->thenReturn($statusInterface1_0);


        return array(
            array($statusableInterface0, $statusInterface0_1, true, true),
            array($statusableInterface1, $statusInterface1_1, false, false),
            array($statusableInterface1, $statusInterface1_1, true, true),
        );

    }
}
