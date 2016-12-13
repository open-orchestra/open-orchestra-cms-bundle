<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

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
     * @Serializer\Type("integer")
     */
    public $nbrUsers;

    /**
     * @Serializer\Type("string")
     */
    public $label;

    /**
     * @Serializer\Type("array<string>")
     */
    protected $roles;

    /**
     * @Serializer\Type("OpenOrchestra\ApiBundle\Facade\SiteFacade")
     */
    public $site;

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\PerimeterFacade>")
     */
    protected $perimeters;

    /**
     * @param string $role
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }
}
