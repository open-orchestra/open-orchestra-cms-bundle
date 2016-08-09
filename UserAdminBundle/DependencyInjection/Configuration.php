<?php

namespace OpenOrchestra\UserAdminBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('open_orchestra_user_admin');

        $rootNode->children()
            ->arrayNode('facades')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('user')->defaultValue('OpenOrchestra\UserAdminBundle\Facade\UserFacade')->end()
                    ->scalarNode('user_collection')->defaultValue('OpenOrchestra\UserAdminBundle\Facade\UserCollectionFacade')->end()
                    ->scalarNode('user_list_group')->defaultValue('OpenOrchestra\UserAdminBundle\Facade\UserListGroupFacade')->end()
                    ->scalarNode('user_list_group_collection')->defaultValue('OpenOrchestra\UserAdminBundle\Facade\UserListGroupCollectionFacade')->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
