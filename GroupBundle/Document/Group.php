<?php

namespace OpenOrchestra\GroupBundle\Document;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface;
use OpenOrchestra\Media\Model\MediaFolderGroupRoleInterface;
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
     * @var Collection $nodeRoles
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface")
     */
    protected $nodeRoles;

    /**
     * @var Collection $mediaFolderRoles
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\Media\Model\MediaFolderGroupRoleInterface")
     */
    protected $mediaFolderRoles;

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
        $this->nodeRoles = new ArrayCollection();
        $this->mediaFolderRoles = new ArrayCollection();
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
    public function getNodeRoles()
    {
        return $this->nodeRoles;
    }

    /**
     * @param NodeGroupRoleInterface $nodeGroupRole
     */
    public function addNodeRole(NodeGroupRoleInterface $nodeGroupRole)
    {
        if ($this->nodeRoles->contains($nodeGroupRole)) {
            $this->nodeRoles->set($this->nodeRoles->indexOf($nodeGroupRole), $nodeGroupRole);
        } else {
            $this->nodeRoles->add($nodeGroupRole);
        }
    }

    /**
     * @param string $nodeId
     * @param string $role
     *
     * @return NodeGroupRoleInterface|null
     */
    public function getNodeRoleByNodeAndRole($nodeId, $role)
    {
        /** @var NodeGroupRoleInterface $nodeRole */
        foreach ($this->nodeRoles as $nodeRole) {
            if ($nodeRole->getNodeId() == $nodeId && $nodeRole->getRole() == $role) {
                return $nodeRole;
            }
        }

        return null;
    }

    /**
     * @param string $nodeId
     * @param string $role
     *
     * @return boolean
     */
    public function hasNodeRoleByNodeAndRole($nodeId, $role)
    {
        return null !== $this->getNodeRoleByNodeAndRole($nodeId, $role);
    }

    /**
     * @return array
     */
    public function getMediaFolderRoles()
    {
        return $this->mediaFolderRoles;
    }

    /**
     * @param MediaFolderGroupRoleInterface $mediaFolderGroupRole
     */
    public function addMediaFolderRole(MediaFolderGroupRoleInterface $mediaFolderGroupRole)
    {
        if ($this->mediaFolderRoles->contains($mediaFolderGroupRole)) {
            $this->mediaFolderRoles->set($this->mediaFolderRoles->indexOf($mediaFolderGroupRole), $mediaFolderGroupRole);
        } else {
            $this->mediaFolderRoles->add($mediaFolderGroupRole);
        }
    }

    /**
     * @param string $folderId
     * @param string $role
     *
     * @return MediaFolderGroupRoleInterface|null
     */
    public function getMediaFolderRoleByMediaFolderAndRole($folderId, $role)
    {
        /** @var MediaFolderGroupRoleInterface $mediaFolderRole */
        foreach ($this->mediaFolderRoles as $mediaFolderRole) {
            if ($mediaFolderRole->getFolderId() == $folderId && $mediaFolderRole->getRole() == $role) {
                return $mediaFolderRole;
            }
        }

        return null;
    }

    /**
     * @param string $folderId
     * @param string $role
     *
     * @return boolean
     */
    public function hasMediaFolderRoleByByMediaFolderAndRole($folderId, $role)
    {
        return null !== $this->getMediaFolderRoleByMediaFolderAndRole($folderId, $role);
    }
}
