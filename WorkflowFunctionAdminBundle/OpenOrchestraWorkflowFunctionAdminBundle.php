<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class OpenOrchestraWorkflowFunctionAdminBundle
 */
class OpenOrchestraWorkflowFunctionAdminBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
