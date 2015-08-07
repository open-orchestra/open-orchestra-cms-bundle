<?php

namespace OpenOrchestra\Backoffice\NavigationPanel\Strategies;

use OpenOrchestra\Backoffice\NavigationPanel\NavigationPanelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class AbstractNavigationPanelStrategy
 */
abstract class AbstractNavigationPanelStrategy implements NavigationPanelInterface
{
    /**
     * @var EngineInterface
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
     * @param EngineInterface $templating
     */
    public function setTemplating(EngineInterface $templating)
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
