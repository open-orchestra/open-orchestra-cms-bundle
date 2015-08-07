<?php

namespace OpenOrchestra\BackofficeBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NavigationPanelExtension
 */
class NavigationPanelExtension extends \Twig_Extension implements ContainerAwareInterface
{
    protected $container;

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

     /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
