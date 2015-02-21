<?php

namespace OpenOrchestra\ApiBundle;

use OpenOrchestra\ApiBundle\DependencyInjection\Compiler\TransformerCompilerPass;
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
    }

}
