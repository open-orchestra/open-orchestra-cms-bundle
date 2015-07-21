<?php

namespace OpenOrchestra\MediaAdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpenOrchestraMediaAdminExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('transformer.yml');
        $loader->load('leftpanel.yml');
        $loader->load('subscriber.yml');
        $loader->load('form.yml');
        $loader->load('manager.yml');
        $loader->load('twig.yml');
        $loader->load('generator.yml');
        $loader->load('display.yml');
        $loader->load('icon.yml');
        $loader->load('extractreference.yml');

        $this->addMediaFieldType($container);
    }

    /**
     * Merge app conf with bundle conf
     *
     * @param ContainerBuilder $container
     */
    protected function addMediaFieldType(ContainerBuilder $container)
    {
        $fieldTypes = array_merge(
            $container->getParameter('open_orchestra_backoffice.field_types'),
            array(
                'orchestra_media' => array(
                    'label' => 'open_orchestra_media_admin.form.field_type.custom_type.media',
                    'type' => 'orchestra_media',
                    'options' => array(
                        'required' => array(
                            'default_value' => false,
                        )
                    )
                )
            )
        );

        $container->setParameter('open_orchestra_backoffice.field_types', $fieldTypes);
    }
}
