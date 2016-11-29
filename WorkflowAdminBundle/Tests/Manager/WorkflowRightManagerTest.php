<?php

namespace OpenOrchestra\WorkflowAdminBundle\Tests\Manager;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\WorkflowAdminBundle\Manager\WorkflowRightManager;
use Phake;

/**
 * Class WorkflowRightManagerTest
 */
class WorkflowRightManagerTest extends AbstractBaseTestCase
{
    /**
     * @var WorkflowRightManager
     */
    protected $workflowRightManager;

    protected $contentTypeRepository;
    protected $workflowRightRepository;
    protected $authorizationWorkflowRightManager;
    protected $workflowRightClass = 'OpenOrchestra\ModelBundle\Document\WorkflowRight';
    protected $referenceClass = 'OpenOrchestra\ModelBundle\Document\Reference';

    protected $authorizationClass = 'OpenOrchestra\ModelBundle\Document\Authorization';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        Phake::when($this->contentTypeRepository)->findAllNotDeletedInLastVersion()->thenReturn(array());
        $this->workflowRightRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\WorkflowRightRepositoryInterface');
        $this->authorizationWorkflowRightManager = Phake::mock('OpenOrchestra\Workflow\Manager\AuthorizationWorkflowRightManager');

        $this->workflowRightManager = new WorkflowRightManager($this->contentTypeRepository, $this->workflowRightRepository, $this->authorizationWorkflowRightManager, $this->workflowRightClass, $this->referenceClass);
    }

    /**
     * test loadOrGenerateByUser
     */
    public function testLoadOrGenerateByUser()
    {

        $userId = 'fakeUserId';

        $this->workflowRightManager->loadOrGenerateByUser($userId);

        Phake::verify($this->contentTypeRepository, Phake::times(1))->findAllNotDeletedInLastVersion();
        Phake::verify($this->workflowRightRepository, Phake::times(1))->findOneByUserId($userId);
        Phake::verify($this->authorizationWorkflowRightManager, Phake::times(1))->cleanAuthorization(Phake::anyParameters());
    }
}
