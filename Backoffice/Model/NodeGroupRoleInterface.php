<?php

namespace OpenOrchestra\Backoffice\Model;

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
