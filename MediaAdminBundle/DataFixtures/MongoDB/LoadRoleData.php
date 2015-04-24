<?php

namespace OpenOrchestra\MediaAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\MediaAdminBundle\LeftPanel\Strategies\TreeFolderPanelStrategy;
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
        $manager->persist($this->generateRole(TreeFolderPanelStrategy::ROLE_ACCESS_TREE_FOLDER));

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
