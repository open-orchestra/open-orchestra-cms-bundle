<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AudienceAnalysisStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ConfigurableContentStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentListStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\FooterStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\LanguageListStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\MenuStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\SubMenuStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\VideoStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContactStrategy;
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
        $this->updateBlockConfiguration($container, $config);

        $container->setParameter('open_orchestra_backoffice.orchestra_choice.front_language', $config['front_languages']);
        $container->setParameter('open_orchestra_user.base_layout', 'OpenOrchestraBackofficeBundle::layout.html.twig');
        $container->setParameter('open_orchestra_user.form_template', 'OpenOrchestraBackofficeBundle::form.html.twig');
        $fixedAttributes = array_merge($config['fixed_attributes'], $container->getParameter('open_orchestra_backoffice.block.fixed_attributes'));
        $container->setParameter('open_orchestra_backoffice.block.fixed_attributes', $fixedAttributes);
        $container->setParameter('open_orchestra_backoffice.block_default_configuration', $config['block_default_configuration']);
        $container->setParameter('open_orchestra_backoffice.template_set', $config['template_set']);
        $container->setParameter('open_orchestra_backoffice.special_page_name', $config['special_page_name']);

        $configurationRoles = $config['configuration_roles'];
        if ($container->hasParameter('open_orchestra_backoffice.configuration.roles')) {
            $configurationRoles = array_merge_recursive($container->getParameter('open_orchestra_backoffice.configuration.roles'), $configurationRoles);
        }
        $container->setParameter('open_orchestra_backoffice.configuration.roles', $configurationRoles);

        $loader->load('services.yml');
        $loader->load('form.yml');
        $loader->load('generator.yml');
        $loader->load('display.yml');
        $loader->load('twig.yml');
        $loader->load('field_type.yml');
        $loader->load('transformer.yml');
        $loader->load('value_transformer.yml');
        $loader->load('subscriber.yml');
        $loader->load('listener.yml');
        $loader->load('block_parameter.yml');
        $loader->load('group.yml');
        $loader->load('voter.yml');
        $loader->load('validator.yml');
        $loader->load('restore_entity.yml');
        $loader->load('remove_trashcan_entity.yml');
        $loader->load('collector.yml');
        $loader->load('usage_finder.yml');
        $loader->load('role_parameter.yml');
        $loader->load('reference.yml');
        $loader->load('auto_publisher.yml');

        if (isset($config['field_types'])) {
            $this->addApplicationFieldTypes($config['field_types'], $container);
        }

        if (isset($config['options'])) {
            $this->addApplicationOptions($config['options'], $container);
        }

        if ('test' == $container->getParameter('kernel.environment')) {
            $loader->load('test_services.yml');
        }

        if (isset($config['front_roles'])) {
            $this->addFrontRoles($config['front_roles'], $container);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param $config
     */
    protected function updateBlockParameter(ContainerBuilder $container, $config)
    {
        $blockType = array(
            FooterStrategy::NAME,
            LanguageListStrategy::NAME,
            MenuStrategy::NAME,
            SubMenuStrategy::NAME,
            ContentListStrategy::NAME,
            ContentStrategy::NAME,
            ConfigurableContentStrategy::NAME,
            TinyMCEWysiwygStrategy::NAME,
            VideoStrategy::NAME,
            AudienceAnalysisStrategy::NAME,
            ContactStrategy::NAME,
        );

        $blocksAlreadySet = array();
        if ($container->hasParameter('open_orchestra.blocks')) {
            $blocksAlreadySet = $container->getParameter('open_orchestra.blocks');
        }

        $blocks = array_merge($config['blocks'], $blockType, $blocksAlreadySet);
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

    /**
     * @param array            $frontRoles
     * @param ContainerBuilder $container
     */
    protected function addFrontRoles(array $frontRoles, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('open_orchestra_backoffice.collector.front_role');
        foreach ($frontRoles as $frontRole) {
            $definition->addMethodCall('addRole', array($frontRole));
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function updateBlockConfiguration(ContainerBuilder $container, array $config)
    {
        $backOfficeBlockConfiguration = array(
            FooterStrategy::NAME => array(
                'category' => 'open_orchestra_backoffice.block_configuration.category.navigation',
            ),
            MenuStrategy::NAME => array(
                'category' => 'open_orchestra_backoffice.block_configuration.category.navigation',
            ),
            SubMenuStrategy::NAME => array(
                'category' => 'open_orchestra_backoffice.block_configuration.category.navigation',
            ),
            LanguageListStrategy::NAME => array(
                'category' => 'open_orchestra_backoffice.block_configuration.category.widget',
            ),
            AudienceAnalysisStrategy::NAME => array(
                'category' => 'open_orchestra_backoffice.block_configuration.category.widget',
            ),
            ContentListStrategy::NAME => array(
                'category' => 'open_orchestra_backoffice.block_configuration.category.content',
            ),
            ContentStrategy::NAME => array(
                'category' => 'open_orchestra_backoffice.block_configuration.category.content',
            ),
            ConfigurableContentStrategy::NAME => array(
                'category' => 'open_orchestra_backoffice.block_configuration.category.content',
            ),
            TinyMCEWysiwygStrategy::NAME => array(
                'category' => 'open_orchestra_backoffice.block_configuration.category.content',
            ),
            ContactStrategy::NAME => array(
                'category' => 'open_orchestra_backoffice.block_configuration.category.contact',
            ),
            VideoStrategy::NAME => array(
                'category' => 'open_orchestra_backoffice.block_configuration.category.media',
            ),
        );

        $blockConfiguration = array();
        if ($container->hasParameter('open_orchestra_backoffice.block_configuration')) {
            $blockConfiguration = $container->getParameter('open_orchestra_backoffice.block_configuration');
        }
        $blockConfiguration = array_merge($config['block_configuration'], $blockConfiguration, $backOfficeBlockConfiguration);
        $container->setParameter('open_orchestra_backoffice.block_configuration', $blockConfiguration);
    }
}
