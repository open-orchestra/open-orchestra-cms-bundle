<?php

namespace OpenOrchestra\UserAdminBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class UserFacade
 */
class UserFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $username;

    /**
     * @Serializer\Type("string")
     */
    public $roles;

    /**
     * @Serializer\Type("string")
     */
    public $groups;
}
