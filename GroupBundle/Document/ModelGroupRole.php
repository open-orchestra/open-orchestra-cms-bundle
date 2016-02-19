<?php

namespace OpenOrchestra\GroupBundle\Document;

use OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class ModelGroupRole
 *
 * @ODM\EmbeddedDocument
 */
class ModelGroupRole implements ModelGroupRoleInterface
{
    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $id;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $type;

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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getAccessType()
    {
        return $this->accessType;
    }

    /**
     * @return bool
     */
    public function isGranted()
    {
        return $this->granted;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @param string $accessType
     */
    public function setAccessType($accessType)
    {
        $this->accessType = $accessType;
    }

    /**
     * @param bool $granted
     */
    public function setGranted($granted)
    {
        $this->granted = $granted;
    }
}
