<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class UserCollectionFacade
 */
class UserCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'users';

    /**
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\UserFacade>")
     */
    protected $users = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addUser(FacadeInterface $facade)
    {
        $this->users[] = $facade;
    }
}
