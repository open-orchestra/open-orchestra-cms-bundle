<?php

namespace OpenOrchestra\ApiBundle;

use OpenOrchestra\ApiBundle\DependencyInjection\Compiler\Oauth2CompilerPass;
use OpenOrchestra\ApiBundle\DependencyInjection\Compiler\TransformerCompilerPass;
use OpenOrchestra\ApiBundle\DependencyInjection\Security\Factory\OAuth2Factory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class OpenOrchestraApiBundle
 */
class OpenOrchestraApiBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TransformerCompilerPass());
        $container->addCompilerPass(new Oauth2CompilerPass());

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new OAuth2Factory());
    }
}
