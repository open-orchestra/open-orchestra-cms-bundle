<?php
namespace OpenOrchestra\GroupBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use OpenOrchestra\GroupBundle\DependencyInjection\Compiler\EntityResolverCompilerPass;
use OpenOrchestra\GroupBundle\DependencyInjection\Compiler\TwigGlobalsCompilerPass;
use OpenOrchestra\GroupBundle\DependencyInjection\Compiler\PerimeterCompilerPass;
use OpenOrchestra\GroupBundle\DependencyInjection\Compiler\GeneratePerimeterCompilerPass;

/**
 * Class OpenOrchestraGroupBundle
 */
class OpenOrchestraGroupBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TwigGlobalsCompilerPass());
        $container->addCompilerPass(new EntityResolverCompilerPass());
        $container->addCompilerPass(new PerimeterCompilerPass());
        $container->addCompilerPass(new GeneratePerimeterCompilerPass());
    }
}
