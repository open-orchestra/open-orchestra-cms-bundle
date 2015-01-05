<?php

namespace PHPOrchestra\Backoffice\LeftPanel\Strategies;

use PHPOrchestra\Backoffice\LeftPanel\LeftPanelInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * Class AbstractLeftPaneStrategy
 */
abstract class AbstractLeftPaneStrategy implements LeftPanelInterface
{
    /**
     * @var TwigEngine
     */
    protected $templating;

    /**
     * @return int
     */
    public function getWeight()
    {
        return 0;
    }

    /**
     * @param TwigEngine $templating
     */
    public function setTemplating(TwigEngine $templating)
    {
        $this->templating = $templating;
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
        return $this->templating->render($view, $parameters);
    }
}
