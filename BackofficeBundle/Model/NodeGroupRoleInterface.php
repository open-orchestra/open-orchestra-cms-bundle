<?php

namespace OpenOrchestra\BackofficeBundle\Model;

/**
 * Interface NodeGroupRoleInterface
 */
interface NodeGroupRoleInterface extends GroupRoleInterface
{
    /**
     * @return string
     */
    public function getNodeId();

    /**
     * @param string $nodeId
     */
    public function setNodeId($nodeId);

}
