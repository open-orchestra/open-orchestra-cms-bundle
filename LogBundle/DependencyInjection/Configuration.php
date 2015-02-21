<?php

namespace OpenOrchestra\LogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('open_orchestra_log');

        $rootNode->children()
            ->arrayNode('document')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('log')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\LogBundle\Document\Log')->end()
                            ->scalarNode('repository')->defaultValue('OpenOrchestra\LogBundle\Repository\LogRepository')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
