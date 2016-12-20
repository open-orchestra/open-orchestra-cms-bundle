<?php

namespace OpenOrchestra\WorkflowAdminBundle;

use OpenOrchestra\WorkflowAdminBundle\DependencyInjection\Compiler\TwigGlobalsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
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
    }
}
