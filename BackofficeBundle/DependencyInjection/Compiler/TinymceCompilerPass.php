<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler;

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
            if ($container->hasParameter('open_orchestra_backoffice.tinymce')) {
                $param = array_merge($param, $container->getParameter('open_orchestra_backoffice.tinymce'));
                if(array_key_exists('content_css', $param) && $container->hasParameter('router.request_context.host')){
                    $param['content_css'] = $container->getParameter('router.request_context.host') . $param['content_css'];
                }
            }
            $container->setParameter('stfalcon_tinymce.config', $param);
        }
    }
}
