<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class GroupFacade
 */
class GroupFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("string")
     */
    public $label;

    /**
     * @Serializer\Type("array<string>")
     */
    protected $roles = array();

    /**
     * @Serializer\Type("OpenOrchestra\ApiBundle\Facade\SiteFacade")
     */
    public $site;

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade>")
     */
    protected $nodeRoles = array();

    /**
     * @Serializer\Type("array<OpenOrchestra\MediaAdminBundle\Facade\MediaFolderGroupRoleFacade>")
     */
    protected $mediaFolderRoles = array();

    /**
     * @param string $role
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }

    /**
     * @param FacadeInterface $facade
     */
    public function addNodeRoles(FacadeInterface $facade)
    {
        $this->nodeRoles[] = $facade;
    }

    /**
     * @return array
     */
    public function getNodeRoles()
    {
        return $this->nodeRoles;
    }

    /**
     * @param FacadeInterface $facade
     */
    public function addMediaFolderRoles(FacadeInterface $facade)
    {
        $this->mediaFolderRoles[] = $facade;
    }

    /**
     * @return array
     */
    public function getMediaFolderRoles()
    {
        return $this->mediaFolderRoles;
    }
}
