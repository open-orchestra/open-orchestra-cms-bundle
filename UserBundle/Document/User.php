<?php

namespace PHPOrchestra\UserBundle\Document;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Document User
 *
 * @MongoDB\Document(collection="user")
 */
class User extends BaseUser
{
    /**
     * @MongoDB\Id()
     */
    protected $id;
}
