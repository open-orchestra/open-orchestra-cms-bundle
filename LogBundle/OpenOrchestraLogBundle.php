<?php

namespace OpenOrchestra\LogBundle;

use OpenOrchestra\LogBundle\DependencyInjection\Compiler\RoleCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class OpenOrchestraLogBundle
 */
class OpenOrchestraLogBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RoleCompilerPass());
    }
}
