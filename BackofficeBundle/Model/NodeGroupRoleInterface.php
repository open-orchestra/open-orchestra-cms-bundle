<?php

namespace OpenOrchestra\BackofficeBundle\Model;

/**
 * Interface NodeGroupRoleInterface
 */
interface NodeGroupRoleInterface
{
    const ACCESS_GRANTED = "granted";
    const ACCESS_DENIED = "denied";
    const ACCESS_INHERIT = "inherit";

    /**
     * @return string
     */
    public function getNodeId();

    /**
     * @return string
     */
    public function getRole();

    /**
     * @return string
     */
    public function getAccessType();

    /**
     * @param string $nodeId
     */
    public function setNodeId($nodeId);

    /**
     * @param string $role
     */
    public function setRole($role);

    /**
     * @param string $accessType
     */
    public function setAccessType($accessType);

    /**
     * @return bool
     */
    public function isGranted();

    /**
     * @param $granted
     */
    public function setGranted($granted);
}
