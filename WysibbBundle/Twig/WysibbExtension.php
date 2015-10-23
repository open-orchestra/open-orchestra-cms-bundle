<?php

namespace OpenOrchestra\WysibbBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;

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

    protected $locale;

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
        return $this->getTemplating()->render('OpenOrchestraWysibbBundle:Script:init.html.twig', array(
                'wysibb_config' => json_encode($this->getWysibbConfigParameter()),
                'wysibb_translations' => json_encode($this->getWysibbTranslationsParameter()),
                'locale' => $this->getLocale()
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
     * @return EngineInterface
     */
    protected function getTemplating()
    {
        return $this->container->get('templating');
    }

    /**
     * Gets Wysibb config parameter from the service container
     *
     * @return array
     */
    protected function getWysibbConfigParameter()
    {
        return $this->container->getParameter('open_orchestra_wysibb.config');
    }

    /**
     * Gets Wysibb translations parameter from the service container
     *
     * @return array
     */
    protected function getWysibbTranslationsParameter()
    {
        return $this->container->getParameter('open_orchestra_wysibb.translations');
    }

    /**
     * Gets locale from master request
     *
     * @return string
     */
    protected function getLocale()
    {
        if (is_null($this->locale)) {
            $requestStack = $this->container->get('request_stack');
            $masterRequest = $requestStack->getMasterRequest();

            $this->locale = $masterRequest->getLocale();
        }

        return $this->locale;
    }
}
