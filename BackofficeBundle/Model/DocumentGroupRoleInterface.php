<?php

namespace OpenOrchestra\BackofficeBundle\Model;

/**
 * Interface DocumentGroupRoleInterface
 */
interface DocumentGroupRoleInterface
{
    const ACCESS_GRANTED = "granted";
    const ACCESS_DENIED = "denied";
    const ACCESS_INHERIT = "inherit";

    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getRole();

    /**
     * @return string
     */
    public function getAccessType();

    /**
     * @return bool
     */
    public function isGranted();

    /**
     * @param string $id
     */
    public function setId($id);

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @param string $role
     */
    public function setRole($role);

    /**
     * @param string $accessType
     */
    public function setAccessType($accessType);

    /**
     * @param $granted
     */
    public function setGranted($granted);
}
