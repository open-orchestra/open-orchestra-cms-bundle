<?php

namespace OpenOrchestra\ApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

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
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\RoleFacade>")
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
