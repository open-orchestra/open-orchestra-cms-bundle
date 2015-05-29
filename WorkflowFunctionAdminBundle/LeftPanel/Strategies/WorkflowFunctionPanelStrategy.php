<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\LeftPanel\Strategies;

use OpenOrchestra\Backoffice\LeftPanel\Strategies\AbstractLeftPanelStrategy;

/**
 * Class WorkflowFunctionPanelStrategy
 */
class WorkflowFunctionPanelStrategy extends AbstractLeftPanelStrategy
{
    const ROLE_ACCESS_WORKFLOWFUNCTION = 'ROLE_ACCESS_WORKFLOWFUNCTION';

    /**
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraWorkflowFunctionAdminBundle:AdministrationPanel:workflowFunctions.html.twig');
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return self::ADMINISTRATION;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'workflow_function';
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return self::ROLE_ACCESS_WORKFLOWFUNCTION;
    }
}
