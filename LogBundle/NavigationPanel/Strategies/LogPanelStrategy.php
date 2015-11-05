<?php

namespace OpenOrchestra\LogBundle\NavigationPanel\Strategies;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AbstractNavigationPanelStrategy;

/**
 * Class LogPanelStrategy
 */
class LogPanelStrategy extends AbstractNavigationPanelStrategy
{
    const ROLE_ACCESS_LOG = 'ROLE_ACCESS_LOG';

    /**
     * @param string $parent
     * @param int    $weight
     */
    public function __construct($parent, $weight)
    {
        parent::__construct('logs', self::ROLE_ACCESS_LOG, $weight, $parent);
    }

    /**
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraLogBundle:AdministrationPanel:logs.html.twig');
    }
}
