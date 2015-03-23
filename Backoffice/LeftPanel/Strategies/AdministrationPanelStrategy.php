<?php

namespace OpenOrchestra\Backoffice\LeftPanel\Strategies;


/**
 * Class AdministrationPanelStrategy
 */
class AdministrationPanelStrategy extends AbstractLeftPaneStrategy
{
    const ROLE_PANEL_CONTENT_TYPE = 'ROLE_PANEL_CONTENT_TYPE';
    const ROLE_PANEL_REDIRECTION = 'ROLE_PANEL_REDIRECTION';
    const ROLE_PANEL_KEYWORD = 'ROLE_PANEL_KEYWORD';
    const ROLE_PANEL_DELETED = 'ROLE_PANEL_DELETED';
    const ROLE_PANEL_STATUS = 'ROLE_PANEL_STATUS';
    const ROLE_PANEL_THEME = 'ROLE_PANEL_THEME';
    const ROLE_PANEL_GROUP = 'ROLE_PANEL_GROUP';
    const ROLE_PANEL_USER = 'ROLE_PANEL_USER';
    const ROLE_PANEL_ROLE = 'ROLE_PANEL_ROLE';
    const ROLE_PANEL_SITE = 'ROLE_PANEL_SITE';
    const ROLE_PANEL_LOG = 'ROLE_PANEL_LOG';

    protected $name;
    protected $role;
    protected $weight;
    protected $parent;

    /**
     * @param string $name
     * @param string $role
     * @param int    $weight
     * @param string $parent
     */
    public function __construct($name, $role, $weight = 0, $parent = self::ADMINISTRATION)
    {
        $this->name = $name;
        $this->role = $role;
        $this->weight = $weight;
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraBackofficeBundle:AdministrationPanel:' . $this->name . '.html.twig');
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
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }
}
