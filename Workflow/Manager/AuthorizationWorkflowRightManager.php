<?php

namespace OpenOrchestra\Workflow\Manager;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Workflow\Model\WorkflowRightInterface;


/**
 * Class AuthorizationWorkflowRightManager
 */
class AuthorizationWorkflowRightManager
{
    /**
     * Constructor
     *
     * @param string $authorizationClass
     */
    public function __construct($authorizationClass)
    {
        $this->authorizationClass = $authorizationClass;
    }

    /**
     * @param array|Collection       $references
     * @param WorkflowRightInterface $workflowRight
     *
     * @return WorkflowRightInterface
     */
    public function cleanAuthorization($references, WorkflowRightInterface $workflowRight)
    {
        $authorizations = $workflowRight->getAuthorizations();
        $indexAuthorizations = $this->indexList($authorizations, "getReferenceId", "AuthorizationInterface");

        $indexReferences = $this->indexList($references, "getId", "ReferenceInterface");

        $AuthorizationNotInReference = array_diff_key($indexAuthorizations, $indexReferences);
        foreach ($AuthorizationNotInReference as $remove) {
            $workflowRight->removeAuthorization($remove);
        }

        $authorizationClass = $this->authorizationClass;
        $ReferenceNotInAuthorization = array_diff_key($indexReferences, $indexAuthorizations);
        foreach ($ReferenceNotInAuthorization as $add) {
            $authorization = new $authorizationClass();
            $authorization->setReferenceId($add->getId());
            $workflowRight->addAuthorization($authorization);
        }

        return $workflowRight;
    }

    /**
     * @param array  $list
     * @param string $getter
     * @param string $type
     *
     * @return array
     */
    private function indexList($list, $getter, $type)
    {
        $referenceArray = array();
        foreach ($list as $value) {
            if($value instanceof $type || method_exists($value, $getter)){
                $id = $value->$getter();
                $referenceArray[$id] = $value;
            }
        }
        return $referenceArray;
    }
}
