<?php

namespace PHPOrchestra\BackofficeBundle;

use PHPOrchestra\BackofficeBundle\DependencyInjection\Compiler\DisplayIconCompilerPass;
use PHPOrchestra\BackofficeBundle\DependencyInjection\Compiler\GenerateFormCompilerPass;
use PHPOrchestra\BackofficeBundle\DependencyInjection\Compiler\TinymceCompilerPass;
use PHPOrchestra\BackofficeBundle\DependencyInjection\Compiler\TwigGlobalsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class PHPOrchestraBackofficeBundle
 */
class PHPOrchestraBackofficeBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new GenerateFormCompilerPass());
        $container->addCompilerPass(new TwigGlobalsCompilerPass());
        $container->addCompilerPass(new TinymceCompilerPass());
        $container->addCompilerPass(new DisplayIconCompilerPass());
    }
}
