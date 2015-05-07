<?php

namespace OpenOrchestra\ApiBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * Class OAuth2Factory
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class OAuth2Factory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $provider = 'open_orchestra_api.security.authentication_provider.oauth2.'.$id;
        $container->setDefinition($provider, new DefinitionDecorator('open_orchestra_api.security.authentication_provider.oauth2'));

        $listenerId = 'open_orchestra_api.security.listener.oauth2.'.$id;
        $container->setDefinition($listenerId, new DefinitionDecorator('open_orchestra_api.security.listener.oauth2'));

        return array($provider, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'oauth2';
    }

    public function addConfiguration(NodeDefinition $builder)
    {
    }
}
