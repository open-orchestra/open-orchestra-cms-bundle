<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection;

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
            ->end()
            ->arrayNode('front_roles')
                ->info('Role than can be given to the user on the Front website')
                ->prototype('scalar')->end()
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
            ->arrayNode('available_color')
                ->info('List of the color available, in the status for instance')
                ->useAttributeAsKey('key')
                ->defaultValue( array(
                    'red' => 'open_orchestra_backoffice.form.status.color.red',
                    'green' => 'open_orchestra_backoffice.form.status.color.green',
                    'orange' => 'open_orchestra_backoffice.form.status.color.orange',
                    'grayDark' => 'open_orchestra_backoffice.form.status.color.grayDark',
                ))
                ->prototype('scalar')->end()
            ->end()
            ->arrayNode('collector')
                ->useAttributeAsKey('key')
                ->defaultValue( array(
                    'workflow_role_in_group' => true
                ))
                ->prototype('scalar')->end()
            ->end()
            ->append($this->addTemplateSetConfiguration())
            ->append($this->addSpecialPageConfiguration())
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
                                ->scalarNode('label')->end()
                                ->scalarNode('path')->end()
                            ->end()
                        ->end()
                        ->prototype('scalar')->end()
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
                    'default' => array(
                        'label' => 'open_orchestra_backoffice.template_set.default.template_name.default',
                        'path' => 'default/default.html'
                    )
                ),
                'styles' => array(
                    'default' => 'open_orchestra_backoffice.template_set.default.style.default'
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
            ->prototype('array')
            ->end();
        $specialPageNames->defaultValue(array(
            'DEFAULT' => 'open_orchestra_backoffice.node.special_page.default'
        ));

        return $specialPageNames;
    }
}
