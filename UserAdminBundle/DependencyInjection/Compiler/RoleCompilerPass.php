<?php

namespace OpenOrchestra\UserAdminBundle\DependencyInjection\Compiler;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\AbstractRoleCompilerPass;
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
            AdministrationPanelStrategy::ROLE_ACCESS_USER,
            AdministrationPanelStrategy::ROLE_ACCESS_CREATE_USER,
            AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_USER,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETE_USER,

        ));
    }
}
