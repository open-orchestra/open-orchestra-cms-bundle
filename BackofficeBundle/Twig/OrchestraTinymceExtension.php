<?php
namespace OpenOrchestra\BackofficeBundle\Twig;

use Stfalcon\Bundle\TinymceBundle\Helper\LocaleHelper;
use Stfalcon\Bundle\TinymceBundle\Twig\Extension\StfalconTinymceExtension;

/**
 * Twig Extension for TinyMce support.
 *
 */
class OrchestraTinymceExtension extends StfalconTinymceExtension
{

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'orchestra_tinymce_init' => new \Twig_Function_Method($this, 'tinymceInit', array('is_safe' => array('html')))
        );
    }

    /**
     * TinyMce initializations
     *
     * @return string
     */
    public function tinymceInit()
    {
        return str_replace('initTinyMCE();', '', parent::tinymceInit());
    }
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'orchestra_tinymce_init';
    }
}

