<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Tests\Manager;

use OpenOrchestra\WorkflowFunctionAdminBundle\Manager\WorkflowRightManager;
use Doctrine\Common\Collections\ArrayCollection;
use Phake;

/**
 * Class WorkflowRightManagerTest
 */
class WorkflowRightManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $contentTypeRepository;
    protected $workflowRightRepository;
    protected $authorizationWorkflowRightManager;
    protected $workflowRightClass = 'OpenOrchestra\WorkflowFunctionBundle\Document\WorkflowRight';
    protected $referenceClass = 'OpenOrchestra\WorkflowFunctionBundle\Document\Reference';

    protected $authorizationClass = 'OpenOrchestra\WorkflowFunctionBundle\Document\Authorization';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        $this->workflowRightRepository = Phake::mock('OpenOrchestra\WorkflowFunction\Repository\WorkflowRightRepositoryInterface');
        $this->authorizationWorkflowRightManager = Phake::mock('OpenOrchestra\WorkflowFunction\Manager\AuthorizationWorkflowRightManager');

        $this->workflowRightManager = new WorkflowRightManager($this->contentTypeRepository, $this->workflowRightRepository, $this->authorizationWorkflowRightManager, $this->workflowRightClass, $this->referenceClass);
    }

    /**
     * test loadOrGenerateByUser
     */
    public function testLoadOrGenerateByUser() {

        $userId = 'fakeUserId';

        $workflowRightInterface = $this->workflowRightManager->loadOrGenerateByUser($userId);

        Phake::verify($this->contentTypeRepository, Phake::times(1))->findAllByDeletedInLastVersion();
        Phake::verify($this->workflowRightRepository, Phake::times(1))->findOneByUserId($userId);
        Phake::verify($this->authorizationWorkflowRightManager, Phake::times(1))->cleanAuthorization(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideWorkflowRight()
    {
        return array(
            array(array('fake_reference', 'fake_both'), array('fake_authorization', 'fake_both'), 1, 1),
            array(array('fake_reference'), array('fake_authorization'), 1, 1),
            array(array(), array('fake_authorization', 'fake_both'), 2, 0),
            array(array('fake_reference', 'fake_both'), array(), 0, 2),
        );
    }
}
