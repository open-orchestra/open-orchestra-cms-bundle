<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler;

use OpenOrchestra\BaseBundle\DependencyInjection\Compiler\AbstractTaggedCompiler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class BlockParameterCompilerPass
 */
class BlockParameterCompilerPass extends AbstractTaggedCompiler implements CompilerPassInterface
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
        $managerName = 'open_orchestra_backoffice.block_parameter_manager';
        $tagName = 'open_orchestra_backoffice.block_parameter.strategy';

        $this->addStrategyToManager($container, $managerName, $tagName);
    }
}
