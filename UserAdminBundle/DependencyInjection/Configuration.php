<?php

namespace OpenOrchestra\UserAdminBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('open_orchestra_user_admin');

        $rootNode->children()
            ->arrayNode('transformer')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('user')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('facade')->defaultValue('OpenOrchestra\UserAdminBundle\Facade\UserFacade')->end()
                        ->end()
                    ->end()
                    ->arrayNode('user_collection')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('facade')->defaultValue('OpenOrchestra\UserAdminBundle\Facade\UserCollectionFacade')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
