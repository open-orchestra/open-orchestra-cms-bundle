<?php

namespace OpenOrchestra\BackofficeBundle\DisplayIcon\Strategies;

use OpenOrchestra\BackofficeBundle\DisplayIcon\DisplayInterface;
use OpenOrchestra\BackofficeBundle\DisplayIcon\DisplayManager;

/**
 * Class AbstractStrategy
 */
abstract class AbstractStrategy implements DisplayInterface
{
    /**
     * @var DisplayManager
     */
    protected $manager;

    /**
     * @param DisplayManager $manager
     */
    public function setManager(DisplayManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Renders a view.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     *
     * @return string
     */
    protected function render($view, array $parameters = array())
    {
        return $this->manager->getTemplating()->render($view, $parameters);
    }
}
