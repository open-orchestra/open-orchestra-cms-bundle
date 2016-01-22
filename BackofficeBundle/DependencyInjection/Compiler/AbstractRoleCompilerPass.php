<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler;

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
        if (!$container->hasDefinition('open_orchestra_backoffice.collector.backoffice_role')) {
            return;
        }

        $definition = $container->getDefinition('open_orchestra_backoffice.collector.backoffice_role');

        foreach ($roles as $role) {
            $definition->addMethodCall('addRole', array($role));
        }
    }
}
