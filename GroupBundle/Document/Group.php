<?php

namespace OpenOrchestra\GroupBundle\Document;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\UserBundle\Document\Group as BaseGroup;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ODM\Document(
 *  collection="users_group",
 *  repositoryClass="OpenOrchestra\GroupBundle\Repository\GroupRepository"
 * )
 */
class Group extends BaseGroup implements GroupInterface
{
    /**
     * @ODM\ReferenceOne(targetDocument="OpenOrchestra\ModelInterface\Model\ReadSiteInterface")
     */
    protected $site;

    /**
     * @var Collection $labels
     *
     * @ODM\Field(type="hash")
     * @ORCHESTRA\Search(key="label", type="multiLanguages")
     */
    protected $labels;

    /**
     * @var Collection $modelRoles
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface", strategy="set")
     */
    protected $modelRoles;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->labels = array();
        $this->roles = array();
        $this->modelRoles = new ArrayCollection();
    }

    /**
     * Clone the element
     */
    public function __clone()
    {
        $this->id = null;
        $modelGroupRoles = $this->modelRoles;
        foreach ($modelGroupRoles as $groupRole) {
            $newGroupRole = clone $groupRole;
            $this->addModelGroupRole($newGroupRole);
        }

        $this->setName($this->cloneLabel($this->name));

        foreach ($this->getLabels() as $language => $label) {
            $this->addLabel($language, $this->cloneLabel($label));
        }
    }

    /**
     * @param ReadSiteInterface|null $site
     */
    public function setSite(ReadSiteInterface $site = null)
    {
        $this->site = $site;
    }

    /**
     * @return ReadSiteInterface|null
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param string $language
     * @param string $label
     */
    public function addLabel($language, $label)
    {
        if (is_string($language) && is_string($label)) {
            $this->labels[$language] = $label;
        }
    }

    /**
     * @param string $language
     */
    public function removeLabel($language)
    {
        if (is_string($language) && isset($this->labels[$language])) {
            unset($this->labels[$language]);
        }
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getLabel($language)
    {
        if (isset($this->labels[$language])) {
            return $this->labels[$language];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param array $labels
     */
    public function setLabels(array $labels)
    {
        foreach ($labels as $language => $label) {
            $this->addLabel($language, $label);
        }
    }

    /**
     * @return array
     */
    public function getModelGroupRoles()
    {
        return $this->modelRoles;
    }

    /**
     * @param string $type
     * @param string $id
     * @param string $role
     *
     * @return ModelGroupRoleInterface|null
     */
    public function getModelGroupRoleByTypeAndIdAndRole($type, $id, $role)
    {
        $key = $this->getKeyModelRoles($id, $type, $role);
        if (true === $this->modelRoles->containsKey($key)) {
            return $this->modelRoles->get($key);
        }

        return null;
    }

    /**
     * @param ModelGroupRoleInterface $modelGroupRole
     */
    public function addModelGroupRole(ModelGroupRoleInterface $modelGroupRole)
    {
        $key = $this->getKeyModelRoles($modelGroupRole->getId(), $modelGroupRole->getType(), $modelGroupRole->getRole());
        $this->modelRoles->set($key, $modelGroupRole);
    }

    /**
     * @param ArrayCollection<ModelGroupRoleInterface> $modelGroupRole
     */
    public function setModelGroupRoles(Collection $modelGroupRoles)
    {
        $correctCollection = true;

        foreach ($modelGroupRoles as $modelGroupRole) {
            if (!($modelGroupRole instanceof ModelGroupRoleInterface)) {
                $correctCollection = false;
                break;
            }
        }

        if ($correctCollection) {
            $this->modelRoles = $modelGroupRoles;
        }
    }

    /**
     * @param string $type
     * @param string $id
     * @param string $role
     *
     * @return boolean
     */
    public function hasModelGroupRoleByTypeAndIdAndRole($type, $id, $role)
    {
        return null !== $this->getModelGroupRoleByTypeAndIdAndRole($type, $id, $role);
    }

    /**
     * @param string $id
     * @param string $type
     * @param string $role
     *
     * @return string
     */
    protected function getKeyModelRoles($id, $type, $role)
    {
        $key = implode(self::SEPARATOR_KEY_MODEL_ROLES, array($id, $type, $role));

        return md5($key);
    }

    /**
     * @param string $label
     *
     * @return string
     */
    protected function cloneLabel($label)
    {
        $patternNameVersion = '/.*_([0-9]+$)/';
        if (0 !== preg_match_all($patternNameVersion, $label, $matches)) {
            $version = (int) $matches[1][0] + 1;
            return preg_replace('/[0-9]+$/', $version, $label);
        }

        return $label . '_2';
    }
}
