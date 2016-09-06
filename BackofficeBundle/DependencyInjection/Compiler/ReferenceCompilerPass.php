<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler;

use OpenOrchestra\BaseBundle\DependencyInjection\Compiler\AbstractTaggedCompiler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ReferenceCompilerPass
 */
class ReferenceCompilerPass extends AbstractTaggedCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $managerName = 'open_orchestra_backoffice.reference.manager';
        $tagName = 'open_orchestra_backoffice.reference.strategy';

        $this->addStrategyToManager($container, $managerName, $tagName);
    }
}
