<?php

namespace OpenOrchestra\LogBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('open_orchestra_log');

        $rootNode->children()
            ->arrayNode('facades')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('log')->defaultValue('OpenOrchestra\LogBundle\Facade\LogFacade')->end()
                    ->scalarNode('log_collection')->defaultValue('OpenOrchestra\LogBundle\Facade\LogCollectionFacade')->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
