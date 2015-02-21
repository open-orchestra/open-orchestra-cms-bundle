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

            $param['tinymce_jquery'] = false;
            $param['include_jquery'] = false;
            $param['selector'] = ".tinymce";
            $param['theme'] = array(
                'simple' => array(
                    "theme"        => "modern",
                    "plugins"      => array(
                        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                        "searchreplace wordcount visualblocks visualchars code fullscreen",
                        "insertdatetime media nonbreaking save table contextmenu directionality",
                        "emoticons template paste textcolor"
                    ),
                    "toolbar1"     => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link",
                    "toolbar2"     => "print preview | forecolor backcolor",
                    "menubar"      => false,
                )
            );

            $container->setParameter('stfalcon_tinymce.config', $param);
        }
    }
}
