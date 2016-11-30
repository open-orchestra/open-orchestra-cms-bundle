<?php

namespace OpenOrchestra\WorkflowAdminBundle\DependencyInjection\Compiler;

use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\AbstractRoleCompilerPass;
use OpenOrchestra\WorkflowAdminBundle\NavigationPanel\Strategies\WorkflowFunctionPanelStrategy;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RoleCompilerPass
 */
class RoleCompilerPass extends AbstractRoleCompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $this->addRoles($container, array(
            WorkflowFunctionPanelStrategy::ROLE_ACCESS_WORKFLOWFUNCTION,
            WorkflowFunctionPanelStrategy::ROLE_ACCESS_CREATE_WORKFLOWFUNCTION,
            WorkflowFunctionPanelStrategy::ROLE_ACCESS_UPDATE_WORKFLOWFUNCTION,
            WorkflowFunctionPanelStrategy::ROLE_ACCESS_DELETE_WORKFLOWFUNCTION,
        ));

        if ($container->hasParameter('open_orchestra_backoffice.role')) {
            $roles = $container->getParameter('open_orchestra_backoffice.role');
            if ($container->hasParameter('open_orchestra_workflow.role')) {
                $roles = array_merge_recursive($roles, $container->getParameter('open_orchestra_workflow.role'));
            }
            $container->setParameter('open_orchestra_backoffice.role', $roles);
        }
    }
}
