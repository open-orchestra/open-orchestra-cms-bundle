<?php

namespace OpenOrchestra\BackofficeBundle\Model;


/**
 * Interface NodeGroupRoleInterface
 */
interface NodeGroupRoleInterface
{
    /**
     * @return string
     */
    public function getNodeId();

    /**
     * @return string
     */
    public function getRole();

    /**
     * @return bool
     */
    public function isGranted();

    /**
     * @param string $nodeId
     */
    public function setNodeId($nodeId);

    /**
     * @param string $role
     */
    public function setRole($role);

    /**
     * @param bool $granted
     */
    public function setGranted($granted);
}
