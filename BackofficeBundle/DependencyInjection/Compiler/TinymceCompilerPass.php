<?php

namespace PHPOrchestra\BackofficeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class TinymceCompilerPass
 */
class TinymceCompilerPass implements CompilerPassInterface
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
        if ($container->hasDefinition('twig.extension.stfalcon_tinymce')) {
            $tinymce = $container->getDefinition('twig.extension.stfalcon_tinymce');

            $param = array(
                'tinymce_jquery' => false,
                'include_jquery' => false,
                'selector' => ".tinymce"
            );

            $container->setParameter($tinymce, $param);
        }
    }
}
