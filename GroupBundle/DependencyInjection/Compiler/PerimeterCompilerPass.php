<?php

namespace OpenOrchestra\GroupBundle\DependencyInjection\Compiler;

use OpenOrchestra\BaseBundle\DependencyInjection\Compiler\AbstractTaggedCompiler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class PerimeterCompilerPass
 */
class PerimeterCompilerPass extends AbstractTaggedCompiler implements CompilerPassInterface
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
        $managerName = 'open_orchestra_group.perimeter_manager';

        $tagName = 'open_orchestra_group.perimeter.strategy';
        $this->addStrategyToManager($container, $managerName, $tagName);
    }
}
