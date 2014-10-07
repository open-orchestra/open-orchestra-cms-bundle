<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class RoleCollection
 */
class RoleCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'roles';

    /**
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\RoleFacade>")
     */
    protected $roles = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addRole(FacadeInterface $facade)
    {
        $this->roles[] = $facade;
    }
}
