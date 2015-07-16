<?php

namespace OpenOrchestra\Backoffice\LeftPanel\Strategies;

/**
 * Class ErrorPagesPanelStrategy
 */
class ErrorPagesPanelStrategy extends AbstractLeftPanelStrategy
{
    const ROLE_ACCESS_TREE_NODE = 'ROLE_ACCESS_TREE_NODE';

    /**
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraBackofficeBundle:AdministrationPanel:errorPages.html.twig');
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return self::EDITORIAL;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'error_pages';
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return self::ROLE_ACCESS_TREE_NODE;
    }
}
