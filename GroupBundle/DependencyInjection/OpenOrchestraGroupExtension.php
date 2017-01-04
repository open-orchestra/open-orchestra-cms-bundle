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
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['facades'] as $transformer => $facade) {
            $container->setParameter('open_orchestra_group.facade.' . $transformer .'.class', $facade);
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('perimeter.yml');
        $loader->load('services.yml');
        $loader->load('form.yml');
        $loader->load('transformer.yml');
        $loader->load('subscriber.yml');
        $loader->load('generate_perimeter.yml');
    }
}
