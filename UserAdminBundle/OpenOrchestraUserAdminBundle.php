<?php

namespace OpenOrchestra\UserAdminBundle;

use OpenOrchestra\UserAdminBundle\DependencyInjection\Compiler\RoleCompilerPass;
use OpenOrchestra\UserAdminBundle\DependencyInjection\Compiler\RoleUsageCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class OpenOrchestraUserAdminBundle
 */
class OpenOrchestraUserAdminBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RoleCompilerPass());
        $container->addCompilerPass(new RoleUsageCompilerPass());
    }
}
