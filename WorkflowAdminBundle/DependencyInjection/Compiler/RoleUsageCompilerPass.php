<?php

namespace OpenOrchestra\WorkflowAdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RoleUsageCompilerPass
 */
class RoleUsageCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('open_orchestra_backoffice.usage_finder.role');
        $definition->addMethodCall('addRepository', array(new Reference('open_orchestra_model.repository.workflow_function')));
    }
}
