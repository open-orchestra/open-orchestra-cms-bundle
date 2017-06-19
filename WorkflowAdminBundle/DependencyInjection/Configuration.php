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
                        'turquoise' => 'open_orchestra_workflow_admin.form.status.color.turquoise',
                        'green'     => 'open_orchestra_workflow_admin.form.status.color.green',
                        'blue'      => 'open_orchestra_workflow_admin.form.status.color.blue',
                        'purple'    => 'open_orchestra_workflow_admin.form.status.color.purple',
                        'dark-grey' => 'open_orchestra_workflow_admin.form.status.color.dark-grey',
                        'yellow'    => 'open_orchestra_workflow_admin.form.status.color.yellow',
                        'orange'    => 'open_orchestra_workflow_admin.form.status.color.orange',
                        'red'       => 'open_orchestra_workflow_admin.form.status.color.red',
                        'grey'      => 'open_orchestra_workflow_admin.form.status.color.grey',
                    ))
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
