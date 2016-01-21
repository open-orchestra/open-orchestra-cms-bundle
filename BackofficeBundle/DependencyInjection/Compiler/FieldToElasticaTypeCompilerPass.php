<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class FieldToElasticaTypeCompilerPass
 */
class FieldToElasticaTypeCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('open_orchestra_elastica.mapper.form')) {
            return ;
        }

        $definition = $container->getDefinition('open_orchestra_elastica.mapper.form');

        $definition->addMethodCall('addMappingConfiguration', array('date', 'date'));
        $definition->addMethodCall('addMappingConfiguration', array('integer', 'double'));
        $definition->addMethodCall('addMappingConfiguration', array('embedded_content', 'object'));
    }
}
