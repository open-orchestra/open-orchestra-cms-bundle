<?php

namespace OpenOrchestra\GroupBundle\Exception;

/**
 * Class NodeGroupRoleNotFoundException
 */
class NodeGroupRoleNotFoundException extends \Exception
{
    /**
     * @param string $role
     * @param string $node
     * @param string $group
     */
    public function __construct($role, $node, $group)
    {
        parent::__construct(
            sprintf('The role %s of node %s was not found in group %s', $role, $node, $group)
        );
    }
}
