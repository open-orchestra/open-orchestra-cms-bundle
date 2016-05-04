<?php

namespace OpenOrchestra\GroupBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\LogBundle\NavigationPanel\Strategies\LogPanelStrategy;
use OpenOrchestra\MediaAdminBundle\NavigationPanel\Strategies\TreeFolderPanelStrategy;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\WorkflowFunctionAdminBundle\NavigationPanel\Strategies\WorkflowFunctionPanelStrategy;

/**
 * Class LoadGroupDemoData
 */
class LoadGroupDemoData extends AbstractLoadGroupData implements OrchestraProductionFixturesInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $group2 = $this->generateGroup('Demo group', 'Demo group', 'Groupe de démo', 'site2', 'group2');
        $manager->persist($group2);

        $adminGroup = $this->generateGroup('Admin group', 'Admin group', 'Groupe admin');
        $adminGroup->addRole(AdministrationPanelStrategy::ROLE_ACCESS_REMOVED_TRASHCAN);
        $adminGroup->addRole(AdministrationPanelStrategy::ROLE_ACCESS_THEME);
        $adminGroup->addRole(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_THEME);
        $adminGroup->addRole(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_THEME);
        $adminGroup->addRole(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_THEME);
        $adminGroup->addRole(TreeFolderPanelStrategy::ROLE_ACCESS_CREATE_MEDIA);
        $adminGroup->addRole(TreeFolderPanelStrategy::ROLE_ACCESS_CREATE_MEDIA_FOLDER);
        $adminGroup->addRole(TreeFolderPanelStrategy::ROLE_ACCESS_DELETE_MEDIA);
        $adminGroup->addRole(TreeFolderPanelStrategy::ROLE_ACCESS_DELETE_MEDIA_FOLDER);
        $adminGroup->addRole(TreeFolderPanelStrategy::ROLE_ACCESS_MEDIA_FOLDER);
        $adminGroup->addRole(TreeFolderPanelStrategy::ROLE_ACCESS_UPDATE_MEDIA);
        $adminGroup->addRole(TreeFolderPanelStrategy::ROLE_ACCESS_UPDATE_MEDIA_FOLDER);
        $adminGroup->addRole(WorkflowFunctionPanelStrategy::ROLE_ACCESS_WORKFLOWFUNCTION);
        $adminGroup->addRole(WorkflowFunctionPanelStrategy::ROLE_ACCESS_CREATE_WORKFLOWFUNCTION);
        $adminGroup->addRole(WorkflowFunctionPanelStrategy::ROLE_ACCESS_UPDATE_WORKFLOWFUNCTION);
        $adminGroup->addRole(WorkflowFunctionPanelStrategy::ROLE_ACCESS_DELETE_WORKFLOWFUNCTION);
        $adminGroup->addRole(LogPanelStrategy::ROLE_ACCESS_LOG);
        $adminGroup->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_ERROR_NODE);
        $adminGroup->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_ERROR_NODE);
        $adminGroup->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_ERROR_NODE);
        $manager->persist($adminGroup);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 610;
    }
}
