<?php

namespace OpenOrchestra\BackofficeBundle;

use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\BlockParameterCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\RemoveTrashcanEntityCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\DisplayBlockCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\FieldToElasticaTypeCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\GenerateFormCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\RestoreEntityCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\TwigGlobalsCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\ValueTransformerCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\ReferenceCompilerPass;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\TemplateCompilerPass;
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
        $container->addCompilerPass(new ReferenceCompilerPass());
        $container->addCompilerPass(new TwigGlobalsCompilerPass());
        $container->addCompilerPass(new BlockParameterCompilerPass());
        $container->addCompilerPass(new DisplayBlockCompilerPass());
        $container->addCompilerPass(new ValueTransformerCompilerPass());
        $container->addCompilerPass(new RestoreEntityCompilerPass());
        $container->addCompilerPass(new RemoveTrashcanEntityCompilerPass());
        $container->addCompilerPass(new FieldToElasticaTypeCompilerPass());
        $container->addCompilerPass(new TemplateCompilerPass());
    }
}
