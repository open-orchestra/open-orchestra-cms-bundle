<?php

namespace OpenOrchestra\ApiBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('open_orchestra_api');

        $rootNode->children()
            ->arrayNode('facades')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('node')->defaultValue('OpenOrchestra\ApiBundle\Facade\NodeFacade')->end()
                    ->scalarNode('node_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\NodeCollectionFacade')->end()
                    ->scalarNode('node_tree')->defaultValue('OpenOrchestra\ApiBundle\Facade\NodeTreeFacade')->end()
                    ->scalarNode('template')->defaultValue('OpenOrchestra\ApiBundle\Facade\TemplateFacade')->end()
                    ->scalarNode('template_flex')->defaultValue('OpenOrchestra\ApiBundle\Facade\TemplateFlexFacade')->end()
                    ->scalarNode('area')->defaultValue('OpenOrchestra\ApiBundle\Facade\AreaFacade')->end()
                    ->scalarNode('area_flex')->defaultValue('OpenOrchestra\ApiBundle\Facade\AreaFlexFacade')->end()
                    ->scalarNode('block')->defaultValue('OpenOrchestra\ApiBundle\Facade\BlockFacade')->end()
                    ->scalarNode('block_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\BlockCollectionFacade')->end()
                    ->scalarNode('ui_model')->defaultValue('OpenOrchestra\ApiBundle\Facade\UiModelFacade')->end()
                    ->scalarNode('content_type')->defaultValue('OpenOrchestra\ApiBundle\Facade\ContentTypeFacade')->end()
                    ->scalarNode('content_type_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\ContentTypeCollectionFacade')->end()
                    ->scalarNode('content')->defaultValue('OpenOrchestra\ApiBundle\Facade\ContentFacade')->end()
                    ->scalarNode('content_attribute')->defaultValue('OpenOrchestra\ApiBundle\Facade\ContentAttributeFacade')->end()
                    ->scalarNode('content_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\ContentCollectionFacade')->end()
                    ->scalarNode('site')->defaultValue('OpenOrchestra\ApiBundle\Facade\SiteFacade')->end()
                    ->scalarNode('site_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\SiteCollectionFacade')->end()
                    ->scalarNode('theme')->defaultValue('OpenOrchestra\ApiBundle\Facade\ThemeFacade')->end()
                    ->scalarNode('theme_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\ThemeCollectionFacade')->end()
                    ->scalarNode('role')->defaultValue('OpenOrchestra\ApiBundle\Facade\RoleFacade')->end()
                    ->scalarNode('role_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\RoleCollectionFacade')->end()
                    ->scalarNode('role_string')->defaultValue('OpenOrchestra\ApiBundle\Facade\RoleFacade')->end()
                    ->scalarNode('role_string_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\RoleCollectionFacade')->end()
                    ->scalarNode('group')->defaultValue('OpenOrchestra\ApiBundle\Facade\GroupFacade')->end()
                    ->scalarNode('node_group_role')->defaultValue('OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade')->end()
                    ->scalarNode('group_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\GroupCollectionFacade')->end()
                    ->scalarNode('redirection')->defaultValue('OpenOrchestra\ApiBundle\Facade\RedirectionFacade')->end()
                    ->scalarNode('redirection_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\RedirectionCollectionFacade')->end()
                    ->scalarNode('status_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\StatusCollectionFacade')->end()
                    ->scalarNode('field_type')->defaultValue('OpenOrchestra\ApiBundle\Facade\FieldTypeFacade')->end()
                    ->scalarNode('status')->defaultValue('OpenOrchestra\ApiBundle\Facade\StatusFacade')->end()
                    ->scalarNode('keyword')->defaultValue('OpenOrchestra\ApiBundle\Facade\KeywordFacade')->end()
                    ->scalarNode('keyword_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\KeywordCollectionFacade')->end()
                    ->scalarNode('link')->defaultValue('OpenOrchestra\ApiBundle\Facade\LinkFacade')->end()
                    ->scalarNode('api_client')->defaultValue('OpenOrchestra\ApiBundle\Facade\ApiClientFacade')->end()
                    ->scalarNode('api_client_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\ApiClientCollectionFacade')->end()
                    ->scalarNode('datatable_translation')->defaultValue('OpenOrchestra\ApiBundle\Facade\DatatableTranslationFacade')->end()
                    ->scalarNode('trash_item')->defaultValue('OpenOrchestra\ApiBundle\Facade\TrashItemFacade')->end()
                    ->scalarNode('trash_item_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\TrashItemCollectionFacade')->end()
                    ->scalarNode('translation')->defaultValue('OpenOrchestra\ApiBundle\Facade\TranslationFacade')->end()
                    ->scalarNode('widget_collection')->defaultValue('OpenOrchestra\ApiBundle\Facade\WidgetCollectionFacade')->end()
                    ->scalarNode('widget')->defaultValue('OpenOrchestra\ApiBundle\Facade\WidgetFacade')->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
