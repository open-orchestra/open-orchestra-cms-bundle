<?php

namespace OpenOrchestra\GroupBundle\Document;

use OpenOrchestra\Backoffice\Model\NodeGroupRoleInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class NodeGroupRole
 *
 * @ODM\EmbeddedDocument
 */
class NodeGroupRole implements NodeGroupRoleInterface
{
    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $nodeId;

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
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @param string $nodeId
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;
    }

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
