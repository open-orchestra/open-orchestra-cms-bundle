<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection;

use OpenOrchestra\BackofficeBundle\DisplayBlock\Strategies\LoginStrategy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;

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
            $config['front_languages'] = array('en' => 'English', 'fr' => 'Français');
        }
        $fixedAttributes = array_merge($config['fixed_attributes'], array('component', 'submit', 'label', 'class', 'id', 'maxAge'));
        $container->setParameter('open_orchestra_backoffice.block.fixed_attributes', $fixedAttributes);

        $container->setParameter('open_orchestra_backoffice.orchestra_choice.front_language', $config['front_languages']);
        $container->setParameter('open_orchestra_backoffice.orchestra_choice.direction', array('h' => 'Horizontal', 'v' => 'Vertical'));
        $container->setParameter('open_orchestra_user.base_layout', 'OpenOrchestraBackofficeBundle::layout.html.twig');

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('form.yml');
        $loader->load('generator.yml');
        $loader->load('display.yml');
        $loader->load('twig.yml');
        $loader->load('fieldtype.yml');
        $loader->load('transformer.yml');
        $loader->load('icon.yml');
        $loader->load('leftpanel.yml');
        $loader->load('subscriber.yml');
        $loader->load('extractreference.yml');
        $loader->load('blockparameter.yml');
        if ('test' == $container->getParameter('kernel.environment')) {
            $loader->load('testservices.yml');
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param $config
     */
    protected function updateBlockParameter(ContainerBuilder $container, $config)
    {
        $blockType = array(
            DisplayBlockInterface::FOOTER,
            LoginStrategy::LOGIN,
            DisplayBlockInterface::LANGUAGE_LIST,
            DisplayBlockInterface::MENU,
            DisplayBlockInterface::SUBMENU,
            DisplayBlockInterface::CONTENT_LIST,
            DisplayBlockInterface::CARROUSEL,
            DisplayBlockInterface::CONTENT,
            DisplayBlockInterface::CONFIGURABLE_CONTENT,
            DisplayBlockInterface::TINYMCEWYSIWYG,
            DisplayBlockInterface::SEARCH,
            DisplayBlockInterface::SEARCH_RESULT,
            DisplayBlockInterface::VIDEO,
            DisplayBlockInterface::GMAP,
            DisplayBlockInterface::ADDTHIS,
            DisplayBlockInterface::AUDIENCE_ANALYSIS,
        );

        if (empty($config['blocks'])) {
            $blocks = $blockType;
        }
        $blocksAlreadySet = array();
        if ($container->hasParameter('open_orchestra.blocks')) {
            $blocksAlreadySet = $container->getParameter('open_orchestra.blocks');
        }
        $blocks = array_merge($blocksAlreadySet, $blocks);
        $container->setParameter('open_orchestra.blocks', $blocks);
    }
}
