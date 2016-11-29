<?php

namespace OpenOrchestra\Workflow\Tests\Manager;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\Workflow\Manager\AuthorizationWorkflowRightManager;
use Doctrine\Common\Collections\ArrayCollection;
use Phake;

/**
 * Class AuthorizationWorkflowRightManagerTest
 */
class AuthorizationWorkflowRightManagerTest extends AbstractBaseTestCase
{
    /**
     * @var AuthorizationWorkflowRightManager
     */
    protected $authorizationWorkflowRightManager;

    protected $authorizationClass = 'OpenOrchestra\ModelBundle\Document\Authorization';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizationWorkflowRightManager = new AuthorizationWorkflowRightManager($this->authorizationClass);
    }

    /**
     * @param array $inReference
     * @param array $inAuthorization
     * @param int   $nbrRemove
     * @param int   $nbrAdd
     *
     * @dataProvider provideWorkflowRight
     */
    public function testCleanAuthorization($inReference, $inAuthorization, $nbrRemove, $nbrAdd)
    {
        $workflowRight = Phake::mock('OpenOrchestra\Workflow\Model\WorkflowRightInterface');

        $authorizations = $this->generateArray($inAuthorization, 'AuthorizationInterface', 'getReferenceId');
        Phake::when($workflowRight)->getAuthorizations()->thenReturn($authorizations);

        $references = $this->generateArray($inReference, "ReferenceInterface", "getId");
        $this->authorizationWorkflowRightManager->cleanAuthorization($references, $workflowRight);

        Phake::verify($workflowRight, Phake::times($nbrRemove))->removeAuthorization(Phake::anyParameters());
        Phake::verify($workflowRight, Phake::times($nbrAdd))->addAuthorization(Phake::anyParameters());
    }

    /**
     * @param array  $inArray
     * @param string $classInterface
     * @param string $getter
     *
     * @return ArrayCollection
     */
    protected function generateArray($inArray, $classInterface, $getter)
    {
        $arrays = new ArrayCollection();
        foreach ($inArray as $classId) {
            $array[] = Phake::mock('OpenOrchestra\Workflow\Model\\' . $classInterface);
            $index = count($array) - 1;
            Phake::when($array[$index])->$getter()->thenReturn($classId);
            $arrays->add($array[$index]);
        }

        return $arrays;
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
