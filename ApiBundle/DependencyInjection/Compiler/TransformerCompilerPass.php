<?php

namespace OpenOrchestra\ApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TransformerCompilerPass
 */
class TransformerCompilerPass implements CompilerPassInterface
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
        if (!$container->hasDefinition('open_orchestra_api.transformer_manager')) {
            return;
        }

        $manager = $container->getDefinition('open_orchestra_api.transformer_manager');
        $strategies = $container->findTaggedServiceIds('open_orchestra_api.transformer.strategy');
        foreach ($strategies as $id => $attributes) {
            $manager->addMethodCall('addTransformer', array(new Reference($id)));
        }
    }
}
