<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ClientConfigurationCompilerPass
 */
class ClientConfigurationCompilerPass implements CompilerPassInterface
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
        $clientConfiguration = $container->getDefinition('open_orchestra_backoffice.manager.client_configuration');

        $clientConfiguration->addMethodCall('addClientConfiguration', array('debug', $container->getParameter('kernel.debug')));
        $clientConfiguration->addMethodCall('addClientConfiguration', array('environment', $container->getParameter('kernel.environment')));
        $clientConfiguration->addMethodCall('addClientConfiguration', array('templateSet', $container->getParameter('open_orchestra_backoffice.template_set')));
    }
}
