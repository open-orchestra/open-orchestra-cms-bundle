<?php

namespace OpenOrchestra\UserBundle\Document;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\UserBundle\Model\UserInterface;

/**
 * Document User
 *
 * @ODM\Document(
 *  collection="user",
 *  repositoryClass="OpenOrchestra\UserBundle\Repository\UserRepository"
 * )
 */
class User extends BaseUser implements UserInterface
{
    //used to avoid conflict between integer and string in languageBySites array indexation
    CONST SITE_ID_PREFIX = 'siteId_';

    /**
     * @ODM\Id()
     */
    protected $id;

    /**
     * @var string $username
     */
    protected $username;

    /**
     * @var string $lastName
     *
     * @ODM\Field(type="string")
     */
    protected $lastName;

    /**
     * @var string $firstName
     *
     * @ODM\Field(type="string")
     */
    protected $firstName;

    /**
     * @var string $language
     *
     * @ODM\Field(type="string")
     */
    protected $language;

    /**
     * @ODM\ReferenceMany(targetDocument="FOS\UserBundle\Model\GroupInterface")
     */
    protected $groups;

    /**
     * @var array $languageBySites
     *
     * @ODM\Field(type="hash")
     */
    protected $languageBySites = array();

    /**
     * @var bool
     *
     * @ODM\Field(type="boolean")
     */
    protected $editAllowed = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setEnabled(true);
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @param array $languageBySites
     */
    public function setLanguageBySites(array $languageBySites)
    {
        $this->languageBySites = $languageBySites;
    }

    /**
     * @param string $siteId
     * @param string $language
     */
    public function setLanguageBySite($siteId, $language)
    {
        $this->languageBySites[self::SITE_ID_PREFIX.$siteId] = $language;
    }

    /**
     * @param string $aliasId
     *
     * @return bool
     */
    public function hasLanguageBySite($siteId)
    {
        return array_key_exists(self::SITE_ID_PREFIX.$siteId, $this->languageBySites);
    }

    /**
     * @return array
     */
    public function getLanguageBySites()
    {
        return $this->languageBySites;
    }

    /**
     * @param bool $editAllowed
     */
    public function setEditAllowed($editAllowed)
    {
        $this->editAllowed = $editAllowed;
    }

    /**
     * @return bool
     */
    public function isEditAllowed()
    {
        return $this->editAllowed;
    }
}
