<?php

namespace OpenOrchestra\BackofficeBundle;

use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\BlockParameterCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\DisplayBlockCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\DisplayIconCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\GenerateFormCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\NavigationPanelCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\TinymceCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\TwigGlobalsCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\ValueTransformerCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\AuthorizeStatusChangeCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class OpenOrchestraBackofficeBundle
 */
class OpenOrchestraBackofficeBundle extends Bundle
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
        $container->addCompilerPass(new NavigationPanelCompilerPass());
        $container->addCompilerPass(new BlockParameterCompilerPass());
        $container->addCompilerPass(new DisplayBlockCompilerPass());
        $container->addCompilerPass(new ValueTransformerCompilerPass());
        $container->addCompilerPass(new AuthorizeStatusChangeCompilerPass());
    }
}
