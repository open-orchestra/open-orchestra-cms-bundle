<?php

namespace PHPOrchestra\BackofficeBundle\DependencyInjection\Compiler;

use PHPOrchestra\BaseBundle\DependencyInjection\Compiler\AbstractTaggedCompiler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class LeftPanelCompilerPass
 */
class LeftPanelCompilerPass extends AbstractTaggedCompiler implements CompilerPassInterface
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
        $managerName = 'php_orchestra_backoffice.left_panel_manager';
        $tagName = 'php_orchestra_backoffice.left_panel.strategy';

        $this->addStrategyToManager($container, $managerName, $tagName);
    }
}
