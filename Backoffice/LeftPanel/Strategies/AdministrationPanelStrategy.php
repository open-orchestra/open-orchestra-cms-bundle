<?php

namespace OpenOrchestra\Backoffice\LeftPanel\Strategies;


/**
 * Class AdministrationPanelStrategy
 */
class AdministrationPanelStrategy extends AbstractLeftPanelStrategy
{
    const ROLE_ACCESS_CONTENT_TYPE = 'ROLE_ACCESS_CONTENT_TYPE';
    const ROLE_ACCESS_REDIRECTION = 'ROLE_ACCESS_REDIRECTION';
    const ROLE_ACCESS_API_CLIENT = 'ROLE_ACCESS_API_CLIENT';
    const ROLE_ACCESS_KEYWORD = 'ROLE_ACCESS_KEYWORD';
    const ROLE_ACCESS_DELETED = 'ROLE_ACCESS_DELETED';
    const ROLE_ACCESS_STATUS = 'ROLE_ACCESS_STATUS';
    const ROLE_ACCESS_THEME = 'ROLE_ACCESS_THEME';
    const ROLE_ACCESS_GROUP = 'ROLE_ACCESS_GROUP';
    const ROLE_ACCESS_USER = 'ROLE_ACCESS_USER';
    const ROLE_ACCESS_ROLE = 'ROLE_ACCESS_ROLE';
    const ROLE_ACCESS_SITE = 'ROLE_ACCESS_SITE';
    const ROLE_ACCESS_LOG = 'ROLE_ACCESS_LOG';

    protected $name;
    protected $role;
    protected $weight;
    protected $parent;
    protected $template;

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
        return $this->render($this->getTemplate());
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

    /**
     * @return string
     */
    protected function getTemplate()
    {
        if ($this->template) {
            return $this->template;
        }

        return 'OpenOrchestraBackofficeBundle:AdministrationPanel:' . $this->name . '.html.twig';
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}
