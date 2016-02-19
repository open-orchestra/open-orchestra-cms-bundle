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
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\ModelGroupRoleFacade>")
     */
    protected $modelRoles = array();

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
    public function addModelRoles(FacadeInterface $facade)
    {
        $this->modelRoles[] = $facade;
    }

    /**
     * @return array
     */
    public function getModelRoles()
    {
        return $this->modelRoles;
    }
}
