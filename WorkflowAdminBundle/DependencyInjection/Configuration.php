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
                    ->scalarNode('workflow_profile')->defaultValue('OpenOrchestra\WorkflowAdminBundle\Facade\WorkflowProfileFacade')->end()
                    ->scalarNode('workflow_profile_collection')->defaultValue('OpenOrchestra\WorkflowAdminBundle\Facade\WorkflowProfileCollectionFacade')->end()
                ->end()
            ->end()
            ->arrayNode('available_color')
                ->info('List of the color available, in the status for instance')
                ->useAttributeAsKey('key')
                    ->defaultValue( array(
                        'turquoise'   => 'open_orchestra_workflow_admin.form.status.color.turquoise',
                        'emerald'     => 'open_orchestra_workflow_admin.form.status.color.emerald',
                        'peter-river' => 'open_orchestra_workflow_admin.form.status.color.peter-river',
                        'amethyst'    => 'open_orchestra_workflow_admin.form.status.color.amethyst',
                        'wet-asphalt' => 'open_orchestra_workflow_admin.form.status.color.wet-asphalt',
                        'sun-flower'  => 'open_orchestra_workflow_admin.form.status.color.sun-flower',
                        'carrot'      => 'open_orchestra_workflow_admin.form.status.color.carrot',
                        'alizarin'    => 'open_orchestra_workflow_admin.form.status.color.alizarin',
                        'concrete'    => 'open_orchestra_workflow_admin.form.status.color.concrete',
                    ))
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
