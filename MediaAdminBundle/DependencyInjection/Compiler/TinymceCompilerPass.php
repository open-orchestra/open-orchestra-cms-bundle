<?php

namespace OpenOrchestra\MediaAdminBundle\DependencyInjection\Compiler;

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
            if ($container->hasParameter('open_orchestra_media_admin.tinymce')) {
                $param = array_merge($param, $container->getParameter('open_orchestra_media_admin.tinymce'));
            }
            if (isset($param["theme"])
                && isset($param["theme"]["simple"])
                && isset($param["theme"]["simple"]["toolbar1"])) {
                $param["theme"]["simple"]["toolbar1"] .= " mediamanager";
                $param["theme"]["simple"]["toolbar1"] = str_replace(' image ', ' ', " " . $param["theme"]["simple"]["toolbar1"]);
            }
            $container->setParameter('stfalcon_tinymce.config', $param);
       }
    }
}
