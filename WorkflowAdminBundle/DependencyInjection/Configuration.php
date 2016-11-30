<?php

namespace OpenOrchestra\WorkflowAdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('open_orchestra_workflow_admin');

        $rootNode->children()
            ->arrayNode('facades')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('workflow_function')->defaultValue('OpenOrchestra\WorkflowAdminBundle\Facade\WorkflowFunctionFacade')->end()
                    ->scalarNode('workflow_function_collection')->defaultValue('OpenOrchestra\WorkflowAdminBundle\Facade\WorkflowFunctionCollectionFacade')->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
