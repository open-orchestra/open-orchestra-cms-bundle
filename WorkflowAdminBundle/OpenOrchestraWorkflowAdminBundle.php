<?php

namespace OpenOrchestra\WorkflowAdminBundle;

use OpenOrchestra\WorkflowAdminBundle\DependencyInjection\Compiler\RoleCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use OpenOrchestra\WorkflowAdminBundle\DependencyInjection\Compiler\RoleUsageCompilerPass;
use OpenOrchestra\WorkflowAdminBundle\DependencyInjection\Compiler\TwigGlobalsCompilerPass;

/**
 * Class OpenOrchestraWorkflowAdminBundle
 */
class OpenOrchestraWorkflowAdminBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TwigGlobalsCompilerPass());
        $container->addCompilerPass(new RoleCompilerPass());
        $container->addCompilerPass(new RoleUsageCompilerPass());
    }
}
