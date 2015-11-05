<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AddThisStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AudienceAnalysisStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ConfigurableContentStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentListStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\FooterStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\GmapStrategy;
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

        $this->updateBlockParameter($container, $config);
        if (empty($config['front_languages'])) {
            $config['front_languages'] = array('en' => 'open_orchestra_backoffice.language.en', 'fr' => 'open_orchestra_backoffice.language.fr', 'de' => 'open_orchestra_backoffice.language.de');
        }
        $fixedAttributes = array_merge($config['fixed_attributes'], array('component', 'submit', 'label', 'class', 'id', 'maxAge'));
        $container->setParameter('open_orchestra_backoffice.block.fixed_attributes', $fixedAttributes);

        $container->setParameter('open_orchestra_backoffice.role.workflow_role_in_group', $config['workflow_role_in_group']);
        $container->setParameter('open_orchestra_backoffice.orchestra_choice.front_language', $config['front_languages']);
        $container->setParameter('open_orchestra_backoffice.orchestra_choice.direction', array('h' => 'Horizontal', 'v' => 'Vertical'));
        $container->setParameter('open_orchestra_user.base_layout', 'OpenOrchestraBackofficeBundle::layout.html.twig');
        $container->setParameter('open_orchestra_user.form_template', 'OpenOrchestraBackofficeBundle::form.html.twig');

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
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

        if (isset($config['field_types'])) {
            $this->addApplicationFieldTypes($config['field_types'], $container);
        }

        if (isset($config['options'])) {
            $this->addApplicationOptions($config['options'], $container);
        }

        if ('test' == $container->getParameter('kernel.environment')) {
            $loader->load('test_services.yml');
        }

        $availableColor = $config['available_color'];
        if (empty($availableColor)) {
            $availableColor = array(
                'red' => 'open_orchestra_backoffice.form.status.color.red',
                'green' => 'open_orchestra_backoffice.form.status.color.green',
                'orange' => 'open_orchestra_backoffice.form.status.color.orange',
            );
        }
        $container->setParameter('open_orchestra_backoffice.choice.available_color', $availableColor);

        $container->setParameter('open_orchestra_backoffice.choice.frequence', array(
            'always' => 'open_orchestra_backoffice.form.changefreq.always',
            'hourly' => 'open_orchestra_backoffice.form.changefreq.hourly',
            'daily' => 'open_orchestra_backoffice.form.changefreq.daily',
            'weekly' => 'open_orchestra_backoffice.form.changefreq.weekly',
            'monthly' => 'open_orchestra_backoffice.form.changefreq.monthly',
            'yearly' => 'open_orchestra_backoffice.form.changefreq.yearly',
            'never' => 'open_orchestra_backoffice.form.changefreq.never'
        ));
    }

    /**
     * @param ContainerBuilder $container
     * @param $config
     */
    protected function updateBlockParameter(ContainerBuilder $container, $config)
    {
        $blockType = array(
            FooterStrategy::FOOTER,
            LanguageListStrategy::LANGUAGE_LIST,
            MenuStrategy::MENU,
            SubMenuStrategy::SUBMENU,
            ContentListStrategy::CONTENT_LIST,
            ContentStrategy::CONTENT,
            ConfigurableContentStrategy::CONFIGURABLE_CONTENT,
            TinyMCEWysiwygStrategy::TINYMCEWYSIWYG,
            VideoStrategy::VIDEO,
            GmapStrategy::GMAP,
            AddThisStrategy::ADDTHIS,
            AudienceAnalysisStrategy::AUDIENCE_ANALYSIS,
            ContactStrategy::CONTACT,
        );

        $blocks = $config['blocks'];
        if (empty($blocks)) {
            $blocks = $blockType;
        }
        $blocksAlreadySet = array();
        if ($container->hasParameter('open_orchestra.blocks')) {
            $blocksAlreadySet = $container->getParameter('open_orchestra.blocks');
        }
        $blocks = array_merge($blocksAlreadySet, $blocks);
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
