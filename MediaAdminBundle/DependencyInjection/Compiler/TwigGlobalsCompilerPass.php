<?php

namespace OpenOrchestra\MediaAdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TwigGlobalsCompilerPass
 */
class TwigGlobalsCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('twig')) {
            $formResources = $container->getParameter('twig.form.resources');
            $formResources[] = 'OpenOrchestraMediaAdminBundle:Form:form_div_layout.html.twig';
            $container->setParameter('twig.form.resources', $formResources);
        }
    }

}
