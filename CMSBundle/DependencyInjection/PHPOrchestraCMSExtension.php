<?php

namespace PHPOrchestra\CMSBundle\DependencyInjection;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PHPOrchestraCMSExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
         $configuration = new Configuration();
         $config = $this->processConfiguration($configuration, $configs);

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
        );
        if (array_key_exists('blocks', $config) && !empty($config['blocks'])) {
            $blockType = $config['blocks'];
        }
        $container->setParameter('php_orchestra.blocks', $blockType);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('config.yml');
        $loader->load('services.yml');
        $loader->load('form.yml');
    }
}
