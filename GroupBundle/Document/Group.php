<?php

namespace OpenOrchestra\GroupBundle\Document;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\UserBundle\Document\Group as BaseGroup;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use OpenOrchestra\ModelInterface\Model\TranslatedValueInterface;
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
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\TranslatedValueInterface", strategy="set")
     * @ORCHESTRA\Search(key="label", type="translatedValue")
     */
    protected $labels;

    /**
     * @var Collection $modelRoles
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface")
     */
    protected $modelRoles;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initCollections();
    }

    /**
     * Clone the element
     */
    public function __clone()
    {
        $this->initCollections();
    }

    /**
     * Initialize collections
     */
    protected function initCollections()
    {
        $this->labels = new ArrayCollection();
        $this->modelRoles = new ArrayCollection();
        $this->roles = array();
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
     * @param TranslatedValueInterface $label
     */
    public function addLabel(TranslatedValueInterface $label)
    {
        $this->labels->set($label->getLanguage(), $label);
    }

    /**
     * @param TranslatedValueInterface $label
     */
    public function removeLabel(TranslatedValueInterface $label)
    {
        $this->labels->remove($label->getLanguage());
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getLabel($language = 'en')
    {
        return $this->labels->get($language)->getValue();
    }

    /**
     * @return Collection
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @return array
     */
    public function getTranslatedProperties()
    {
        return array(
            'getLabels'
        );
    }

    /**
     * @return array
     */
    public function getModelRoles()
    {
        return $this->modelRoles;
    }

    /**
     * @param ModelGroupRoleInterface $modelGroupRole
     */
    public function addModelRole(ModelGroupRoleInterface $modelGroupRole)
    {
        if ($this->modelRoles->contains($modelGroupRole)) {
            $this->modelRoles->set($this->modelRoles->indexOf($modelGroupRole), $modelGroupRole);
        } else {
            $this->modelRoles->add($modelGroupRole);
        }
    }

    /**
     * @param string $type
     * @param string $id
     * @param string $role
     *
     * @return ModelGroupRoleInterface|null
     */
    public function getModelRoleByTypeAndIdAndRole($type, $id, $role)
    {
        /** @var ModelGroupRoleInterface $modelRole */
        foreach ($this->modelRoles as $modelRole) {
            if ($modelRole->getType() == $type && $modelRole->getId() == $id && $modelRole->getRole() == $role) {
                return $modelRole;
            }
        }

        return null;
    }

    /**
     * @param string $type
     * @param string $id
     * @param string $role
     *
     * @return boolean
     */
    public function hasModelRoleByTypeAndIdAndRole($type, $id, $role)
    {
        return null !== $this->getModelRoleByTypeAndIdAndRole($type, $id, $role);
    }
}
