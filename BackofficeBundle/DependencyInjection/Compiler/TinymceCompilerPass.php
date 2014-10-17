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
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('twig.extension.stfalcon_tinymce')) {
            $param = $container->getParameter('stfalcon_tinymce.config');

            $param['tinymce_jquery'] = false;
            $param['include_jquery'] = false;
            $param['selector'] = ".tinymce";

            $container->setParameter('stfalcon_tinymce.config', $param);
        }
    }
}
