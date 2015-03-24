<?php

namespace OpenOrchestra\BackofficeBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\LeftPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\Backoffice\LeftPanel\Strategies\ContentTypeForContentPanelStrategy;
use OpenOrchestra\Backoffice\LeftPanel\Strategies\GeneralNodesPanelStrategy;
use OpenOrchestra\Backoffice\LeftPanel\Strategies\TreeFolderPanelStrategy;
use OpenOrchestra\Backoffice\LeftPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\Backoffice\LeftPanel\Strategies\TreeTemplatePanelStrategy;
use OpenOrchestra\ModelBundle\Document\Role;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;

/**
 * Class LoadRoleData
 */
class LoadRoleData implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $manager->persist($this->generateRole(GeneralNodesPanelStrategy::ROLE_PANEL_GENERAL_NODE));
        $manager->persist($this->generateRole(TreeNodesPanelStrategy::ROLE_PANEL_TREE_NODE));
        $manager->persist($this->generateRole(TreeFolderPanelStrategy::ROLE_PANEL_TREE_FOLDER));
        $manager->persist($this->generateRole(TreeTemplatePanelStrategy::ROLE_PANEL_TREE_TEMPLATE));
        $manager->persist($this->generateRole(ContentTypeForContentPanelStrategy::ROLE_PANEL_CONTENT_TYPE_FOR_CONTENT));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_PANEL_CONTENT_TYPE));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_PANEL_REDIRECTION));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_PANEL_KEYWORD));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_PANEL_DELETED));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_PANEL_STATUS));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_PANEL_THEME));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_PANEL_GROUP));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_PANEL_USER));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_PANEL_SITE));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_PANEL_ROLE));
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_PANEL_LOG));

        $manager->flush();
    }

    /**
     * @param string $roleName
     *
     * @return Role
     */
    protected function generateRole($roleName)
    {
        $role = new Role();
        $role->setName($roleName);
        $role->addDescription($this->generateTranslatedValue('en', $roleName));
        $role->addDescription($this->generateTranslatedValue('fr', $roleName));
        $role->addDescription($this->generateTranslatedValue('de', $roleName));
        $role->addDescription($this->generateTranslatedValue('es', $roleName));

        return $role;
    }

    /**
     * Generate a translatedValue
     *
     * @param string $language
     * @param string $value
     *
     * @return TranslatedValue
     */
    protected function generateTranslatedValue($language, $value)
    {
        $label = new TranslatedValue();
        $label->setLanguage($language);
        $label->setValue($value);

        return $label;
    }
}
