<?php

namespace OpenOrchestra\GroupBundle\EventListener;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface;

abstract class AbstractNodeGroupRoleListener extends ContainerAware
{
    protected $nodeGroupRoleClass;

    /**
     * @param string $nodeGroupRoleClass
     */
    public function __construct($nodeGroupRoleClass)
    {
        $this->nodeGroupRoleClass = $nodeGroupRoleClass;
    }

    /**
     * @return array
     */
    public function getNodeRoles()
    {
        $collector = $this->container->get('open_orchestra_backoffice.collector.role');

        return $collector->getRolesByType('node');
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function getNodeAccessType(NodeInterface $node)
    {
        $accessType = NodeGroupRoleInterface::ACCESS_INHERIT;
        if (NodeInterface::ROOT_NODE_ID === $node->getNodeId()) {
            $accessType = NodeGroupRoleInterface::ACCESS_GRANTED;
        }

        return $accessType;
    }

    /**
     * @param string $nodeId
     * @param string $role
     * @param string $accessType
     *
     * @return NodeGroupRoleInterface
     */
    protected function createNodeGroupRole($nodeId, $role, $accessType)
    {

        /** @var $nodeGroupRole NodeGroupRoleInterface */
        $nodeGroupRole = new $this->nodeGroupRoleClass();
        $nodeGroupRole->setNodeId($nodeId);
        $nodeGroupRole->setRole($role);
        $nodeGroupRole->setAccessType($accessType);

        return $nodeGroupRole;
    }
}