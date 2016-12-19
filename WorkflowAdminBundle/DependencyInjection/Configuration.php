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
                    ->scalarNode('status')->defaultValue('OpenOrchestra\WorkflowAdminBundle\Facade\StatusFacade')->end()
                    ->scalarNode('status_collection')->defaultValue('OpenOrchestra\WorkflowAdminBundle\Facade\StatusCollectionFacade')->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
