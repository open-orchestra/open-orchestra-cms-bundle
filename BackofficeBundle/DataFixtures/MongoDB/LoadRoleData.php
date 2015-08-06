<?php

namespace OpenOrchestra\BackofficeBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\ContentTypeForContentPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GeneralNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;

/**
 * Class LoadRoleData
 */
class LoadRoleData extends AbstractLoadRoleData
{
    /**
     * @param ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $manager->persist($this->generateRole(GeneralNodesPanelStrategy::ROLE_ACCESS_GENERAL_NODE));
        $manager->persist($this->generateRole(TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE));
        $manager->persist($this->generateRole(TreeTemplatePanelStrategy::ROLE_ACCESS_TREE_TEMPLATE));
        $manager->persist($this->generateRole(ContentTypeForContentPanelStrategy::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_ACCESS_CONTENT_TYPE));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_ACCESS_REDIRECTION));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_ACCESS_API_CLIENT));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_ACCESS_KEYWORD));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_ACCESS_DELETED));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_ACCESS_STATUS));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_ACCESS_THEME));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_ACCESS_GROUP));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_ACCESS_SITE));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_ACCESS_ROLE));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_ACCESS_LOG));

        $manager->flush();
    }
}
