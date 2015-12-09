<?php

namespace OpenOrchestra\ApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class
 * }
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('open_orchestra_api');

        $rootNode->children()
            ->arrayNode('transformer')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('node')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('facade')->defaultValue('OpenOrchestra\ApiBundle\Facade\NodeFacade')->end()
                        ->end()
                    ->end()
                    ->arrayNode('node_collection')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('facade')->defaultValue('OpenOrchestra\ApiBundle\Facade\NodeCollectionFacade')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
