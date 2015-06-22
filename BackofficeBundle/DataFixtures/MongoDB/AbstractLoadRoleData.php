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
