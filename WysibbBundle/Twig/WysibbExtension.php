<?php

namespace OpenOrchestra\WysibbBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WysibbExtension
 */
class WysibbExtension extends \Twig_Extension implements ContainerAwareInterface
{
    /**
     * Container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('wysibb_init', array($this, 'wysibbInit'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Wysibb initializations
     *
     * @return string
     */
    public function wysibbInit()
    {
        $config = array(
            "buttons" => "bold,italic,underline,|,img,link,|,code,quote,quote,quote"
        );

        return $this->getTemplating()->render('OpenOrchestraWysibbBundle:Script:init.html.twig', array(
            'wysibb_config' => json_encode($config)
            )
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'wysibb';
    }

    /**
     * Sets the container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Gets the templating service.
     *
     * @return object The templating service
     */
    protected function getTemplating()
    {
        return $this->container->get('templating');
    }

    /**
     * Get parameters from the service container
     *
     * @param string $name
     *
     * @return mixed
     */
    protected function getParameter($name)
    {
        return $this->container->getParameter($name);
    }
}
