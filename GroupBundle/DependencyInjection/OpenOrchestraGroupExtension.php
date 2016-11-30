<?php
namespace OpenOrchestra\GroupBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class OpenOrchestraGroupExtension
 */
class OpenOrchestraGroupExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('perimeter.yml');
        $loader->load('service.yml');
        $loader->load('form.yml');
        $loader->load('transformer.yml');
        $loader->load('authorize_status_change.yml');
    }
}
