<?php

namespace OpenOrchestra\Backoffice\NavigationPanel\Strategies;

/**
 * Class TopMenuPanelStrategy
 */
class TopMenuPanelStrategy extends AdministrationPanelStrategy
{
    protected $hasFunctionality;

    /**
     * @param string $name
     * @param int    $weight
     */
    public function __construct($name, $weight = 0, $hasFunctionality = false)
    {
        parent::__construct($name, $weight, self::ROOT_MENU);
        $this->hasFunctionality = $hasFunctionality;
    }

    /**
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraBackofficeBundle:BackOffice:Include/NavigationPanel/Menu/' . $this->name . '.html.twig', array('hasFunctionality' => $this->hasFunctionality));
    }
}
