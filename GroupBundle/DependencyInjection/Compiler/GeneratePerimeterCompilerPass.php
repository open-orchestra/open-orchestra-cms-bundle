<?php

namespace OpenOrchestra\GroupBundle\DependencyInjection\Compiler;

use OpenOrchestra\BaseBundle\DependencyInjection\Compiler\AbstractTaggedCompiler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class GeneratePerimeterCompilerPass
 */
class GeneratePerimeterCompilerPass extends AbstractTaggedCompiler implements CompilerPassInterface
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
        $managerName = 'open_orchestra_group.generate_perimeter_manager';

        $tagName = 'open_orchestra_group.generate_perimeter.strategy';
        $this->addStrategyToManager($container, $managerName, $tagName);
    }
}
