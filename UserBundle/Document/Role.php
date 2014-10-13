<?php

namespace PHPOrchestra\UserBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use PHPOrchestra\ModelBundle\Model\StatusInterface;

/**
 * Class Role
 *
 * @ODM\Document(
 *   collection="role",
 *   repositoryClass="PHPOrchestra\UserBundle\Repository\RoleRepository"
 * )
 */
class Role
{
    /**
     * @ODM\Id()
     */
    protected $id;

    /**
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @var StatusInterface
     *
     * @ODM\ReferenceOne(targetDocument="PHPOrchestra\ModelBundle\Document\Status", inversedBy="fromRoles")
     */
    protected $fromStatus;

    /**
     * @var StatusInterface
     *
     * @ODM\ReferenceOne(targetDocument="PHPOrchestra\ModelBundle\Document\Status", inversedBy="toRoles")
     */
    protected $toStatus;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return StatusInterface
     */
    public function getToStatus()
    {
        return $this->toStatus;
    }

    /**
     * @param StatusInterface $toStatus
     */
    public function setToStatus(StatusInterface $toStatus)
    {
        $this->toStatus = $toStatus;
        $toStatus->addToRole($this);
    }

    /**
     * @return StatusInterface
     */
    public function getFromStatus()
    {
        return $this->fromStatus;
    }

    /**
     * @param StatusInterface $fromStatus
     */
    public function setFromStatus(StatusInterface $fromStatus)
    {
        $this->fromStatus = $fromStatus;
        $fromStatus->addFromRole($this);
    }
}
