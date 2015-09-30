<?php

namespace OpenOrchestra\BackofficeBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use OpenOrchestra\ModelBundle\Document\Role;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;

/**
 * Class AbstractLoadRoleData
 */
abstract class AbstractLoadRoleData implements FixtureInterface
{
    /**
     * @param string $roleName
     * @param array  $translations
     * 
     * @return Role
     */
    protected function generateRole($roleName, $translations = array())
    {
        $role = new Role();
        $role->setName($roleName);
        $role->addDescription($this->generateTranslatedValue('en', $translations, $roleName));
        $role->addDescription($this->generateTranslatedValue('fr', $translations, $roleName));

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
    protected function generateTranslatedValue($language, $translations, $defaultValue)
    {
        $label = new TranslatedValue();
        $label->setLanguage($language);
        $value = isset($translations[$language]) ? $translations[$language] : $defaultValue;
        $label->setValue($value);

        return $label;
    }
}
