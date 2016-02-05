<?php

namespace OpenOrchestra\GroupBundle\Document;

use OpenOrchestra\BackofficeBundle\Model\GroupRoleInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class AbstractGroupRole
 */
class AbstractGroupRole implements GroupRoleInterface
{
    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $role;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $accessType;

    /**
     * @var bool
     *
     * @ODM\Field(type="boolean")
     */
    protected $granted;

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getAccessType()
    {
        return $this->accessType;
    }

    /**
     * @param string $accessType
     */
    public function setAccessType($accessType)
    {
        $this->accessType = $accessType;
    }

    /**
     * @return bool
     */
    public function isGranted()
    {
        return $this->granted;
    }

    /**
     * @param bool $granted
     */
    public function setGranted($granted)
    {
        $this->granted = $granted;
    }
}
