<?php

namespace OpenOrchestra\UserAdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpenOrchestraUserAdminExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['facades'] as $transformer => $facade) {
            $container->setParameter('open_orchestra_user_admin.facade.' . $transformer .'.class', $facade);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('transformer.yml');
        $loader->load('form.yml');
        $loader->load('oauth2.yml');
        $loader->load('subscriber.yml');

        $this->addOrchestraHierarchy($container, $loader);
    }

    /**
     * Add Open Orchestra role hierarchy
     *
     * @param ContainerBuilder $container
     */
    protected function addOrchestraHierarchy(ContainerBuilder $container, LoaderInterface $loader)
    {
        $loader->load('security.yml');

        $orchestraHierarchy = $container->getParameter('open_orchestra_user_admin.role_hierarchy');
        $roleHierarchy = array();
        if ($container->hasParameter('security.role_hierarchy.roles')) {
            $roleHierarchy = $container->getParameter('security.role_hierarchy.roles');
        }

        $container->setParameter('security.role_hierarchy.roles', array_merge($orchestraHierarchy, $roleHierarchy));
    }
}
