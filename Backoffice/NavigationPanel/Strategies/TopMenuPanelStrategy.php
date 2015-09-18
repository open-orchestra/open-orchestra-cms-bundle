<?php

namespace OpenOrchestra\Backoffice\NavigationPanel\Strategies;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AbstractNavigationPanelStrategy;

/**
 * Class TopMenuPanelStrategy
 */
class TopMenuPanelStrategy extends AbstractNavigationPanelStrategy
{
    protected $name;
    protected $weight;

    public function __construct($name, $weight = 0)
    {
        $this->name = $name;
        $this->parent = parent::ROOT_MENU;
        $this->weight = $weight;
    }

    /**
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraBackofficeBundle:BackOffice:Include/NavigationPanel/Menu/' . $this->name . '.html.twig');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return null;
    }
}
