<?php

namespace OpenOrchestra\GroupBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * Class EntityResolverCompilerPass
 */
class EntityResolverCompilerPass implements  CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $resourcePath = '.';

        foreach ($container->getResources() as $resource) {
            $resourcePath = $resource->getResource();
            if (is_string($resourcePath)) {
                if (strpos($resourcePath, 'open-orchestra-cms-bundle')
                    && strpos($resourcePath, 'GroupBundle')
                    && strpos($resourcePath, 'config')
                    && strpos($resourcePath, 'yml') === false
                    && strpos($resourcePath, 'validation') === false
                ) {
                    break;
                }
            }
        }

        $defaultResolveDocument = Yaml::parse(file_get_contents($resourcePath . '/resolve_document.yml'))['resolve_target_documents'];

        $definition = $container->findDefinition('doctrine_mongodb.odm.listeners.resolve_target_document');
        $definitionCalls = $definition->getMethodCalls();
        foreach ($defaultResolveDocument as $interface => $class) {
            if (! $this->resolverExist($definitionCalls, $interface)) {
                $definition->addMethodCall('addResolveTargetDocument', array($interface, $class, array()));
            }
        }
    }

    /**
     * Check if model interface are already resolved
     *
     * @param $methodCalls
     * @param $interface
     *
     * @return bool
     */
    protected function resolverExist($methodCalls, $interface)
    {
        foreach ($methodCalls as $call) {
            if ($call[0] === 'addResolveTargetDocument' && $call[1][0] === $interface) {
                return true;
            }
        }

        return false;
    }
}
