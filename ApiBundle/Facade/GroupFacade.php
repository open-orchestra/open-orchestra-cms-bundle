<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

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
     * @Serializer\Type("array<string>")
     */
    protected $roles = array();

    /**
     * @Serializer\Type("OpenOrchestra\ApiBundle\Facade\SiteFacade")
     */
    public $site;

    /**
     * @param string $role
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }
}
