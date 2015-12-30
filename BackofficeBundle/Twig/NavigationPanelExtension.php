<?php

namespace OpenOrchestra\BackofficeBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class NavigationPanelExtension
 */
class NavigationPanelExtension extends \Twig_Extension implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('navigation_panel', array($this, 'displayNavigationPanel'), array('is_safe' => array('html'))),
        );
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return array(
            'navigation_panel' =>
                array(
                    'root_menu' => AbstractNavigationPanelStrategy::ROOT_MENU
                )
        );
    }

    /**
     * @return string
     */
    public function displayNavigationPanel()
    {
        return $this->container->get('open_orchestra_backoffice.navigation_panel_manager')->show();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'navigation_panel';
    }
}
