<?php

namespace OpenOrchestra\MediaAdminBundle;

use OpenOrchestra\MediaAdminBundle\DependencyInjection\Compiler\ExtractReferenceCompilerPass;
use OpenOrchestra\MediaAdminBundle\DependencyInjection\Compiler\TinymceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use OpenOrchestra\MediaAdminBundle\DependencyInjection\Compiler\TwigGlobalsCompilerPass;

/**
 * Class OpenOrchestraMediaAdminBundle
 */
class OpenOrchestraMediaAdminBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ExtractReferenceCompilerPass());
        $container->addCompilerPass(new TwigGlobalsCompilerPass());
        $container->addCompilerPass(new TinymceCompilerPass());
    }
}
