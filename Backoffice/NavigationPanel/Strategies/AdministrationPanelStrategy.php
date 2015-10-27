<?php

namespace OpenOrchestra\Backoffice\NavigationPanel\Strategies;


/**
 * Class AdministrationPanelStrategy
 */
class AdministrationPanelStrategy extends AbstractNavigationPanelStrategy
{
    const ROLE_ACCESS_CONTENT_TYPE = 'ROLE_ACCESS_CONTENT_TYPE';
    const ROLE_ACCESS_CREATE_CONTENT_TYPE = 'ROLE_ACCESS_CREATE_CONTENT_TYPE';
    const ROLE_ACCESS_UPDATE_CONTENT_TYPE = 'ROLE_ACCESS_UPDATE_CONTENT_TYPE';
    const ROLE_ACCESS_DELETE_CONTENT_TYPE = 'ROLE_ACCESS_DELETE_CONTENT_TYPE';
    const ROLE_ACCESS_REDIRECTION = 'ROLE_ACCESS_REDIRECTION';
    const ROLE_ACCESS_API_CLIENT = 'ROLE_ACCESS_API_CLIENT';
    const ROLE_ACCESS_CREATE_API_CLIENT = 'ROLE_ACCESS_CREATE_API_CLIENT';
    const ROLE_ACCESS_UPDATE_API_CLIENT = 'ROLE_ACCESS_UPDATE_API_CLIENT';
    const ROLE_ACCESS_DELETE_API_CLIENT = 'ROLE_ACCESS_DELETE_API_CLIENT';
    const ROLE_ACCESS_KEYWORD = 'ROLE_ACCESS_KEYWORD';
    const ROLE_ACCESS_CREATE_KEYWORD = 'ROLE_ACCESS_CREATE_KEYWORD';
    const ROLE_ACCESS_UPDATE_KEYWORD = 'ROLE_ACCESS_UPDATE_KEYWORD';
    const ROLE_ACCESS_DELETE_KEYWORD = 'ROLE_ACCESS_DELETE_KEYWORD';
    const ROLE_ACCESS_DELETED = 'ROLE_ACCESS_DELETED';
    const ROLE_ACCESS_RESTORE = 'ROLE_ACCESS_RESTORE';
    const ROLE_ACCESS_STATUS = 'ROLE_ACCESS_STATUS';
    const ROLE_ACCESS_THEME = 'ROLE_ACCESS_THEME';
    const ROLE_ACCESS_GROUP = 'ROLE_ACCESS_GROUP';
    const ROLE_ACCESS_USER = 'ROLE_ACCESS_USER';
    const ROLE_ACCESS_ROLE = 'ROLE_ACCESS_ROLE';
    const ROLE_ACCESS_SITE = 'ROLE_ACCESS_SITE';
    const ROLE_ACCESS_CREATE_SITE = 'ROLE_ACCESS_CREATE_SITE';
    const ROLE_ACCESS_UPDATE_SITE = 'ROLE_ACCESS_UPDATE_SITE';
    const ROLE_ACCESS_DELETE_SITE = 'ROLE_ACCESS_DELETE_SITE';
    const ROLE_ACCESS_LOG = 'ROLE_ACCESS_LOG';

    protected $template;

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
    protected function getTemplate()
    {
        if ($this->template) {
            return $this->template;
        }

        return 'OpenOrchestraBackofficeBundle:BackOffice:Include/NavigationPanel/Menu/Administration/' . $this->name . '.html.twig';
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}
