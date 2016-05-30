<?php

namespace OpenOrchestra\LogBundle\NavigationPanel\Strategies;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AbstractNavigationStrategy;

/**
 * Class LogPanelStrategy
 */
class LogPanelStrategy extends AbstractNavigationStrategy
{
    const ROLE_ACCESS_LOG = 'ROLE_ACCESS_LOG';

    /**
     * @param string $parent
     * @param int    $weight
     */
    public function __construct($parent, $weight)
    {
        parent::__construct('logs', $weight, $parent, self::ROLE_ACCESS_LOG);
    }

    /**
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraLogBundle:AdministrationPanel:logs.html.twig');
    }
}
