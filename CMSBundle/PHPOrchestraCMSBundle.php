<?php

namespace PHPOrchestra\CMSBundle;

use PHPOrchestra\CMSBundle\DependencyInjection\Compiler\DisplayBlockCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class PHPOrchestraCMSBundle
 */
class PHPOrchestraCMSBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DisplayBlockCompilerPass());
    }
}
