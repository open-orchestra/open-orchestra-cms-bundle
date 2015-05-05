<?php

namespace OpenOrchestra\ApiBundle\DependencyInjection\Compiler;

use OpenOrchestra\BaseBundle\DependencyInjection\Compiler\AbstractTaggedCompiler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class Oauth2CompilerPass
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class Oauth2CompilerPass extends AbstractTaggedCompiler implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $managerName = 'open_orchestra_api.oauth2.authorization_server';
        $tagName = 'open_orchestra_api.oauth2.strategy';

        $this->addStrategyToManager($container, $managerName, $tagName);
    }
}
