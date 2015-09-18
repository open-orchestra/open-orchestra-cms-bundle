<?php

namespace OpenOrchestra\Backoffice\NavigationPanel\Strategies;

use OpenOrchestra\Backoffice\NavigationPanel\NavigationPanelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class AbstractNavigationPanelStrategy
 */
abstract class AbstractNavigationPanelStrategy implements NavigationPanelInterface
{
    const ROOT_MENU = 'root_menu';

    /**
     * @var EngineInterface
     */
    protected $templating;

    protected $parent;
    protected $weight = 0;

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * return string
     */
    public function getParent()
    {
        if (!is_null($this->parent)) {
            return $this->parent;
        }

        return self::ROOT_MENU;
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
