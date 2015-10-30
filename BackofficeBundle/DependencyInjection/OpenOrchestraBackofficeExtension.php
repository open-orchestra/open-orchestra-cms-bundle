<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpenOrchestraBackofficeExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('parameters.yml');

        $this->updateBlockParameter($container, $config);

        $container->setParameter('open_orchestra_backoffice.orchestra_choice.front_language', $config['front_languages']);
        $container->setParameter('open_orchestra_user.base_layout', 'OpenOrchestraBackofficeBundle::layout.html.twig');
        $container->setParameter('open_orchestra_user.form_template', 'OpenOrchestraBackofficeBundle::form.html.twig');
        $container->setParameter('open_orchestra_backoffice.collector.role.workflow_role_in_group', $config['collector']['workflow_role_in_group']);
        $fixedAttributes = array_merge($config['fixed_attributes'], $container->getParameter('open_orchestra_backoffice.block.fixed_attributes'));
        $container->setParameter('open_orchestra_backoffice.block.fixed_attributes', $fixedAttributes);

        $loader->load('services.yml');
        $loader->load('form.yml');
        $loader->load('generator.yml');
        $loader->load('display.yml');
        $loader->load('twig.yml');
        $loader->load('field_type.yml');
        $loader->load('transformer.yml');
        $loader->load('icon.yml');
        $loader->load('navigation_panel.yml');
        $loader->load('value_transformer.yml');
        $loader->load('subscriber.yml');
        $loader->load('block_parameter.yml');
        $loader->load('group.yml');
        $loader->load('voter.yml');
        $loader->load('validator.yml');
        $loader->load('initializer.yml');
        $loader->load('authorize_status_change.yml');
        $loader->load('authorize_edition.yml');
        $loader->load('restore_entity.yml');
        $loader->load('collector.yml');

        if (isset($config['field_types'])) {
            $this->addApplicationFieldTypes($config['field_types'], $container);
        }

        if (isset($config['options'])) {
            $this->addApplicationOptions($config['options'], $container);
        }

        if ('test' == $container->getParameter('kernel.environment')) {
            $loader->load('test_services.yml');
        }

        $container->setParameter('open_orchestra_backoffice.dashboard_widgets', $config['dashboard_widgets']);
        $container->setParameter('open_orchestra_backoffice.choice.available_color', $config['available_color']);
    }

    /**
     * @param ContainerBuilder $container
     * @param $config
     */
    protected function updateBlockParameter(ContainerBuilder $container, $config)
    {
        $blocksAlreadySet = array();
        if ($container->hasParameter('open_orchestra.blocks')) {
            $blocksAlreadySet = $container->getParameter('open_orchestra.blocks');
        }
        $blocks = array_merge($blocksAlreadySet, $config['blocks']);
        $container->setParameter('open_orchestra.blocks', $blocks);
    }

    /**
     * Merge app conf with bundle conf
     *
     * @param array            $appFieldTypes
     * @param ContainerBuilder $container
     */
    protected function addApplicationFieldTypes($appFieldTypes, ContainerBuilder $container)
    {
        $fieldTypes = array_merge(
            $container->getParameter('open_orchestra_backoffice.field_types'),
            $appFieldTypes
        );

        $container->setParameter('open_orchestra_backoffice.field_types', $fieldTypes);
    }

    /**
     * Merge app conf with bundle conf
     *
     * @param array            $appOptions
     * @param ContainerBuilder $container
     */
    protected function addApplicationOptions($appOptions, ContainerBuilder $container)
    {
        $options = array_merge(
            $container->getParameter('open_orchestra_backoffice.options'),
            $appOptions
        );

        $container->setParameter('open_orchestra_backoffice.options', $options);
    }
}
