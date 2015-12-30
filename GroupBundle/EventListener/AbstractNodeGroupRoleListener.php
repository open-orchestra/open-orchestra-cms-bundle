<?php

namespace OpenOrchestra\GroupBundle\EventListener;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

abstract class AbstractNodeGroupRoleListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

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
    protected function getNodeRoles()
    {
        $collector = $this->container->get('open_orchestra_backoffice.collector.backoffice_role');

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
