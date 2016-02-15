<?php

namespace OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\ContentTypeForContentPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GeneralNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplateFlexPanelStrategy;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RoleCompilerPass
 */
class RoleCompilerPass extends AbstractRoleCompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $roles = array(
            ContentTypeForContentPanelStrategy::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT,
            ContentTypeForContentPanelStrategy::ROLE_ACCESS_CREATE_CONTENT_TYPE_FOR_CONTENT,
            ContentTypeForContentPanelStrategy::ROLE_ACCESS_UPDATE_CONTENT_TYPE_FOR_CONTENT,
            ContentTypeForContentPanelStrategy::ROLE_ACCESS_DELETE_CONTENT_TYPE_FOR_CONTENT,
            AdministrationPanelStrategy::ROLE_ACCESS_CONTENT_TYPE,
            AdministrationPanelStrategy::ROLE_ACCESS_CREATE_CONTENT_TYPE,
            AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_CONTENT_TYPE,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETE_CONTENT_TYPE,
            AdministrationPanelStrategy::ROLE_ACCESS_REDIRECTION,
            AdministrationPanelStrategy::ROLE_ACCESS_CREATE_REDIRECTION,
            AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_REDIRECTION,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETE_REDIRECTION,
            AdministrationPanelStrategy::ROLE_ACCESS_API_CLIENT,
            AdministrationPanelStrategy::ROLE_ACCESS_CREATE_API_CLIENT,
            AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_API_CLIENT,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETE_API_CLIENT,
            TreeTemplatePanelStrategy::ROLE_ACCESS_TREE_TEMPLATE,
            TreeTemplatePanelStrategy::ROLE_ACCESS_CREATE_TEMPLATE,
            TreeTemplatePanelStrategy::ROLE_ACCESS_UPDATE_TEMPLATE,
            TreeTemplatePanelStrategy::ROLE_ACCESS_DELETE_TEMPLATE,
            TreeTemplateFlexPanelStrategy::ROLE_ACCESS_TREE_TEMPLATE_FLEX,
            TreeTemplateFlexPanelStrategy::ROLE_ACCESS_CREATE_TEMPLATE_FLEX,
            TreeTemplateFlexPanelStrategy::ROLE_ACCESS_UPDATE_TEMPLATE_FLEX,
            TreeTemplateFlexPanelStrategy::ROLE_ACCESS_DELETE_TEMPLATE_FLEX,
            GeneralNodesPanelStrategy::ROLE_ACCESS_TREE_GENERAL_NODE,
            GeneralNodesPanelStrategy::ROLE_ACCESS_UPDATE_GENERAL_NODE,
            AdministrationPanelStrategy::ROLE_ACCESS_KEYWORD,
            AdministrationPanelStrategy::ROLE_ACCESS_CREATE_KEYWORD,
            AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_KEYWORD,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETE_KEYWORD,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETED,
            AdministrationPanelStrategy::ROLE_ACCESS_RESTORE,
            AdministrationPanelStrategy::ROLE_ACCESS_STATUS,
            AdministrationPanelStrategy::ROLE_ACCESS_CREATE_STATUS,
            AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_STATUS,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETE_STATUS,
            AdministrationPanelStrategy::ROLE_ACCESS_THEME,
            AdministrationPanelStrategy::ROLE_ACCESS_CREATE_THEME,
            AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_THEME,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETE_THEME,
            AdministrationPanelStrategy::ROLE_ACCESS_GROUP,
            AdministrationPanelStrategy::ROLE_ACCESS_CREATE_GROUP,
            AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_GROUP,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETE_GROUP,
            AdministrationPanelStrategy::ROLE_ACCESS_USER,
            AdministrationPanelStrategy::ROLE_ACCESS_ROLE,
            AdministrationPanelStrategy::ROLE_ACCESS_CREATE_ROLE,
            AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_ROLE,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETE_ROLE,
            TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE,
            TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE,
            TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE,
            TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE,
            TreeNodesPanelStrategy::ROLE_ACCESS_MOVE_NODE,
            AdministrationPanelStrategy::ROLE_ACCESS_SITE,
            AdministrationPanelStrategy::ROLE_ACCESS_CREATE_SITE,
            AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_SITE,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETE_SITE,

        );

        $this->addRoles($container, $roles);
    }
}
