<?php

namespace PHPOrchestra\CMSBundle\DependencyInjection\Compiler;

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
            $twig = $container->getDefinition('twig');
            $twig->addMethodCall('addGlobal', array('context', new Reference('php_orchestra_cms.context_manager')));

            $formResources = $container->getParameter('twig.form.resources');
            $formResources[] = 'PHPOrchestraCMSBundle:Form:contentTypeFields.html.twig';
            $container->setParameter('twig.form.resources', $formResources);
        }
    }

}
