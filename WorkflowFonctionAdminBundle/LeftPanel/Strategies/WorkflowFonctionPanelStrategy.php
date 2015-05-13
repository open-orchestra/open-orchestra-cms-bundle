<?php

namespace OpenOrchestra\WorkflowFonctionAdminBundle\LeftPanel\Strategies;

use OpenOrchestra\Backoffice\LeftPanel\Strategies\AbstractLeftPanelStrategy;
use OpenOrchestra\WorkflowFonction\Repository\WorkflowFonctionRepositoryInterface;

/**
 * Class WorkflowFonctionPanelStrategy
 */
class WorkflowFonctionPanelStrategy extends AbstractLeftPanelStrategy
{
    const ROLE_ACCESS_WORKFLOWFONCTION = 'ROLE_ACCESS_WORKFLOWFONCTION';

    /**
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraWorkflowFonctionAdminBundle:AdministrationPanel:workflowFonctions.html.twig');
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
        return 'workflowfonctions';
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return self::ROLE_ACCESS_WORKFLOWFONCTION;
    }
}
