<?php

namespace OpenOrchestra\LogBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('open_orchestra_log');

        $rootNode->children()
            ->arrayNode('transformer')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('log')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('facade')->defaultValue('OpenOrchestra\LogBundle\Facade\LogFacade')->end()
                        ->end()
                    ->end()
                    ->arrayNode('log_collection')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('facade')->defaultValue('OpenOrchestra\LogBundle\Facade\LogCollectionFacade')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
