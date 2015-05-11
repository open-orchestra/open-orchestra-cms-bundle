<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler;

use OpenOrchestra\BaseBundle\DependencyInjection\Compiler\AbstractTaggedCompiler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DisplayBlockCompilerPass
 */
class DisplayBlockCompilerPass extends AbstractTaggedCompiler implements CompilerPassInterface
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
        $managerName = 'open_orchestra_backoffice.display_block_manager';

        $tagName = 'open_orchestra_display.display_block.strategy';
        $this->addStrategyToManager($container, $managerName, $tagName);

        $tagName = 'open_orchestra_backoffice.display_block.strategy';
        $this->addStrategyToManager($container, $managerName, $tagName);
    }
}
