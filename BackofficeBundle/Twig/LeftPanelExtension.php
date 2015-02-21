<?php

namespace OpenOrchestra\BackofficeBundle\Twig;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class LeftPanelExtension
 */
class LeftPanelExtension extends \Twig_Extension
{
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('left_panel', array($this, 'displayLeftPanel'), array('is_safe' => array('html'))),
        );
    }

    /**
     * @return string
     */
    public function displayLeftPanel()
    {
        return $this->container->get('open_orchestra_backoffice.left_panel_manager')->show();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'left_panel';
    }
}
