<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection;

use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('open_orchestra_backoffice');

        $rootNode->children()
            ->arrayNode('front_languages')
                ->info('Add the available languages in the Front Office, default (en, fr, de)')
                ->useAttributeAsKey('key')
                ->defaultValue(array(
                    'en'=>'open_orchestra_backoffice.language.en',
                    'fr'=>'open_orchestra_backoffice.language.fr',
                    'de' => 'open_orchestra_backoffice.language.de',
                ))
                ->prototype('scalar')->end()
            ->end()
            ->arrayNode('blocks')
                ->info('Set the available block types for this application')
                ->prototype('scalar')->end()
            ->end()
            ->arrayNode('block_default_configuration')
                ->useAttributeAsKey('key')
                ->defaultValue(array(
                    'maxAge'=>'600',
                    'searchable'=>'false',
                ))
                ->prototype('scalar')->end()
            ->end()
            ->arrayNode('fixed_attributes')
                ->info('Add the global block attributes')
                ->prototype('scalar')->end()
                ->defaultValue(array(
                    'maxAge',
                    'label',
                    'style',
                    'code'
                ))
            ->end()
            ->arrayNode('trash_item_type')
                ->info('List of trash item type')
                ->prototype('scalar')->end()
                ->defaultValue(array(
                    NodeInterface::TRASH_ITEM_TYPE,
                    ContentInterface::TRASH_ITEM_TYPE
                ))
            ->end()
            ->append($this->addFieldTypesParameter())
            ->append($this->addFieldTypesSearchableView())
            ->arrayNode('options')
                ->info('Array of content attributes options')
                ->useAttributeAsKey('option_name')
                ->prototype('array')
                    ->children()
                        ->scalarNode('type')->isRequired()->end()
                        ->scalarNode('label')->isRequired()->end()
                        ->booleanNode('required')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end()
            ->append($this->addTemplateSetConfiguration())
            ->append($this->addSpecialPageConfiguration())
            ->append($this->addConfigurationRoleConfiguration())
            ->append($this->addBlockConfiguration())
            ->append($this->addConfigurationFrontRoleConfiguration())
        ->end();

        return $treeBuilder;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function addFieldTypesSearchableView()
    {
        $builder = new TreeBuilder();
        $fieldSearchableView = $builder->root('field_searchable_view');
        $fieldSearchableView
            ->info("List of field's type searchable view (for content list)")
            ->useAttributeAsKey('searchable view_name')
            ->prototype('array')
                ->children()
                    ->scalarNode('label')->isRequired()->end()
                    ->scalarNode('view')->isRequired()->end()
                ->end()
            ->end()
        ;

        return $fieldSearchableView;
    }


    /**
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function addFieldTypesParameter()
    {
        $builder = new TreeBuilder();
        $fieldTypes = $builder->root('field_types');

        $fieldTypes
            ->info('Array of content attributes (for content types)')
            ->useAttributeAsKey('field_name')
            ->prototype('array')
                ->children()
                    ->scalarNode('label')->isRequired()->end()
                    ->scalarNode('type')->isRequired()->end()
                    ->scalarNode('search')->end()
                    ->scalarNode('deserialize_type')->end()
                    ->arrayNode('default_value')
                        ->children()
                            ->scalarNode('type')->end()
                            ->arrayNode('options')
                                ->children()
                                    ->scalarNode('label')->end()
                                    ->booleanNode('required')->defaultTrue()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('options')
                    ->useAttributeAsKey('option_name')
                    ->requiresAtLeastOneElement()
                        ->prototype('array')
                            ->children()
                                ->scalarNode('default_value')->isRequired()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $fieldTypes;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function addTemplateSetConfiguration()
    {
        $builder = new TreeBuilder();
        $templateSet = $builder->root('template_set');

        $templateSet
            ->info('Array of template set to describe a template. Used to render a node')
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('label')->end()
                    ->arrayNode('templates')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->arrayNode('areas')
                                    ->info('list of editable areas in template')
                                    ->prototype('scalar')->end()
                                    ->isRequired()
                                ->end()
                                ->scalarNode('label')->end()
                                ->scalarNode('path')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('styles')
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();

        $templateSet->defaultValue(array(
            'default' => array(
                'label' => 'open_orchestra_backoffice.template_set.default.label',
                'templates' => array(
                    'home' => array(
                        'areas' => array('main'),
                        'label' => 'open_orchestra_backoffice.template_set.default.template_name.home',
                        'path' => '/bundles/openorchestrabackoffice/templateSet/default/home.html'
                    ),
                    'column_left' => array(
                        'areas' => array('main', 'left_column'),
                        'label' => 'open_orchestra_backoffice.template_set.default.template_name.column_left',
                        'path' => '/bundles/openorchestrabackoffice/templateSet/default/column_left.html'
                    ),
                    'column_right' => array(
                        'areas' => array('main', 'right_column'),
                        'label' => 'open_orchestra_backoffice.template_set.default.template_name.column_right',
                        'path' => '/bundles/openorchestrabackoffice/templateSet/default/column_right.html'
                    )
                ),
                'styles' => array(
                    'default' => 'open_orchestra_backoffice.template_set.default.style.default',
                    'col-33'  => 'open_orchestra_backoffice.template_set.default.style.33%',
                    'col-30'  => 'open_orchestra_backoffice.template_set.default.style.30%',
                    'col-50'  => 'open_orchestra_backoffice.template_set.default.style.50%',
                    'col-70'  => 'open_orchestra_backoffice.template_set.default.style.70%',
                )
            )
        ));

        return $templateSet;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function addSpecialPageConfiguration()
    {
        $builder = new TreeBuilder();
        $specialPageNames = $builder->root('special_page_name');
        $specialPageNames
            ->info('Array of available special page names')
            ->useAttributeAsKey('name')
            ->prototype('scalar')
            ->end();
        $specialPageNames->defaultValue(array(
            'DEFAULT' => 'open_orchestra_backoffice.node.special_page.default',
        ));

        return $specialPageNames;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function addConfigurationRoleConfiguration()
    {
        $builder = new TreeBuilder();
        $configurationRole = $builder->root('configuration_roles');

        $configurationRole
            ->info('Array to describe roles')
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('label')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('help')->end()
                                ->scalarNode('icon')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $configurationRole;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function addBlockConfiguration()
    {
        $builder = new TreeBuilder();
        $blockConfiguration = $builder->root('block_configuration');

        $blockConfiguration
            ->info('Configure block description (category)')
            ->useAttributeAsKey('block_name')
            ->prototype('array')
                ->children()
                    ->scalarNode('category')
                        ->info('Translation key of block category (navigation, search, ...)')
                    ->end()
                    ->scalarNode('name')
                        ->info('Translation key of block component')
                    ->end()
                    ->scalarNode('description')
                        ->info('Translation key of description of block component')
                    ->end()
                ->end()
            ->end();

        return $blockConfiguration;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function addConfigurationFrontRoleConfiguration()
    {
        $builder = new TreeBuilder();
        $configurationRole = $builder->root('front_roles');

        $configurationRole
            ->info('Configure front role configuration')
            ->useAttributeAsKey('name')
            ->prototype('scalar')
            ->end();

        $configurationRole->defaultValue(array());

        return $configurationRole;
    }
}
