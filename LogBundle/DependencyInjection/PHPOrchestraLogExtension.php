<?php

namespace PHPOrchestra\LogBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PHPOrchestraLogExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['document'] as $class => $content) {
            if (is_array($content)) {
                $container->setParameter('php_orchestra_log.document.' . $class . '.class', $content['class']);

                $container->register('php_orchestra_log.repository.' . $class, $content['repository'])
                    ->setFactoryService('doctrine.odm.mongodb.document_manager')
                    ->setFactoryMethod('getRepository')
                    ->addArgument($content['class']);
            }
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('subscriber.yml');
        $loader->load('processor.yml');
        $loader->load('transformer.yml');
    }
}
