<?php

namespace OpenOrchestra\WorkflowAdminBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
    }
}
