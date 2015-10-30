<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AbstractRoleCompilerPass
 */
abstract class AbstractRoleCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     * @param array            $roles
     */
    protected function addRoles(ContainerBuilder $container, array $roles = array())
    {
        if (!$container->hasDefinition('open_orchestra_backoffice.collector.role')) {
            return;
        }

        $definition = $container->getDefinition('open_orchestra_backoffice.collector.role');

        foreach ($roles as $role) {
            $definition->addMethodCall('addRole', array($role));
        }

        $this->addRoleHierarchieSuperAdmin($container, $roles);
    }

    /**
     * @param ContainerBuilder $container
     * @param array $roles
     */
    protected function addRoleHierarchieSuperAdmin(ContainerBuilder $container, array $roles)
    {
        $rolesHierarchy = $container->getParameter('security.role_hierarchy.roles');
        if (isset($rolesHierarchy[UserInterface::ROLE_SUPER_ADMIN])) {
            $rolesHierarchy[UserInterface::ROLE_SUPER_ADMIN] = array_merge($rolesHierarchy["ROLE_SUPER_ADMIN"], $roles);
            $container->setParameter('security.role_hierarchy.roles', $rolesHierarchy);
        }
    }
}
