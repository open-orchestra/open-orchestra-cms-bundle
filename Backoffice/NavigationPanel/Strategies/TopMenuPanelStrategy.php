<?php

namespace OpenOrchestra\Backoffice\NavigationPanel\Strategies;

/**
 * Class TopMenuPanelStrategy
 */
class TopMenuPanelStrategy extends AdministrationPanelStrategy
{
    /**
     * @param string $name
     * @param int    $weight
     */
    public function __construct($name, $weight = 0)
    {
        parent::__construct($name, null, $weight, self::ROOT_MENU);
    }

    /**
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraBackofficeBundle:BackOffice:Include/NavigationPanel/Menu/' . $this->name . '.html.twig');
    }
}
