<?php

namespace PHPOrchestra\BackofficeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PHPOrchestraBackofficeExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (!empty($config['front_languages'])) {
            $container->setParameter('php_orchestra_backoffice.orchestra_choice.front_language', $config['front_languages']);
        } else {
            $container->setParameter('php_orchestra_backoffice.orchestra_choice.front_language', array(
                'en' => 'English',
                'fr' => 'French'
            ));
        }

        $container->setParameter('php_orchestra_backoffice.orchestra_choice.direction', array(
            'h' => 'Horizontal',
            'v' => 'Vertical',
        ));

        $blockType = array(
            DisplayBlockInterface::CARROUSEL,
            DisplayBlockInterface::CONTACT,
            DisplayBlockInterface::FOOTER,
            DisplayBlockInterface::HEADER,
            DisplayBlockInterface::MENU,
            DisplayBlockInterface::NEWS,
            DisplayBlockInterface::SAMPLE,
            DisplayBlockInterface::SEARCH,
            DisplayBlockInterface::SEARCH_RESULT,
            DisplayBlockInterface::TINYMCEWYSIWYG,
            DisplayBlockInterface::CONFIGURABLE_CONTENT,
        );
        if (array_key_exists('blocks', $config) && !empty($config['blocks'])) {
            $blockType = $config['blocks'];
        }
        $container->setParameter('php_orchestra.blocks', $blockType);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('form.yml');
        $loader->load('generator.yml');
        $loader->load('display.yml');
    }
}
