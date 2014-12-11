<?php

namespace PHPOrchestra\BackofficeBundle\DisplayIcons\Strategies;

use PHPOrchestra\BackofficeBundle\DisplayIcons\DisplayIconInterface;
use PHPOrchestra\BackofficeBundle\DisplayIcons\DisplayIconManager;

/**
 * Class AbstractStrategy
 */
abstract class AbstractStrategy implements DisplayIconInterface
{
    /**
     * @var DisplayIconManager
     */
    protected $manager;

    /**
     * @param DisplayIconManager $manager
     */
    public function setManager(DisplayIconManager $manager)
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
