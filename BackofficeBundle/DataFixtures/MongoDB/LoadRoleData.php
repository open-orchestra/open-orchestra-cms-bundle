<?php

namespace OpenOrchestra\BackofficeBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\ContentTypeForContentPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GeneralNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadRoleData
 */
class LoadRoleData extends AbstractLoadRoleData implements OrchestraProductionFixturesInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $this->storeRole(
            $manager,
            GeneralNodesPanelStrategy::ROLE_ACCESS_GENERAL_NODE,
            'Manage the transversal pages',
            'Gérer les pages transverses'
        );
        $this->storeRole(
            $manager,
            TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE,
            'Manage pages',
            'Gérer les pages'
        );
        $this->storeRole(
            $manager,
            TreeTemplatePanelStrategy::ROLE_ACCESS_TREE_TEMPLATE,
            'Manage page templates',
            'Gérer les gabarits de pages'
        );
        $this->storeRole(
            $manager,
            ContentTypeForContentPanelStrategy::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT,
            'Manage contents',
            'Gérer les contenus'
        );
        $this->storeRole(
            $manager,
            AdministrationPanelStrategy::ROLE_ACCESS_CONTENT_TYPE,
            'Manage content types',
            'Gérer les types de contenus'
        );
        $this->storeRole(
            $manager,
            AdministrationPanelStrategy::ROLE_ACCESS_REDIRECTION,
            'Manage HTTP redirections',
            'Gérer les redirections HTTP'
        );
        $this->storeRole(
            $manager,
            AdministrationPanelStrategy::ROLE_ACCESS_API_CLIENT,
            'Manage API access',
            'Gérer les accès à l\'API'
        );
        $this->storeRole(
            $manager,
            AdministrationPanelStrategy::ROLE_ACCESS_KEYWORD,
            'Manage keywords (tags)',
            'Gérer les mots-clefs (tags)'
        );
        $this->storeRole(
            $manager,
            AdministrationPanelStrategy::ROLE_ACCESS_DELETED,
            'Manage recycle bin',
            'Gérer la corbeille'
        );
        $this->storeRole(
            $manager,
            AdministrationPanelStrategy::ROLE_ACCESS_STATUS,
            'Manage publication statuses',
            'Gérer les états de publication'
        );
        $this->storeRole(
            $manager,
            AdministrationPanelStrategy::ROLE_ACCESS_THEME,
            'Manage themes',
            'Gérer les thèmes'
        );
        $this->storeRole(
            $manager,
            AdministrationPanelStrategy::ROLE_ACCESS_GROUP,
            'Manage user groups',
            'Gérer les groupes utilisateurs'
        );
        $this->storeRole(
            $manager,
            AdministrationPanelStrategy::ROLE_ACCESS_SITE,
            'Manage web sites',
            'Gérer les sites'
        );
        $this->storeRole(
            $manager,
            AdministrationPanelStrategy::ROLE_ACCESS_ROLE,
            'Manage roles',
            'Gérer les rôles'
        );
        $this->storeRole(
            $manager,
            AdministrationPanelStrategy::ROLE_ACCESS_LOG,
            'View administration log',
            'Visualiser le journal d\'administration'
        );

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param string        $role
     * @param string        $enTranslation
     * @param string        $frTranslation
     */
    protected function storeRole(ObjectManager $manager, $role, $enTranslation, $frTranslation)
    {
        $manager->persist(
            $this->generateRole(
                $role,
                array('en' => $enTranslation, 'fr' => $frTranslation)
            )
        );
    }
}
