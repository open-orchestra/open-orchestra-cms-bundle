<?php

namespace OpenOrchestra\GroupBundle\EventListener;

use OpenOrchestra\GroupBundle\Exception\NodeGroupRoleNotFoundException;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\Backoffice\Model\NodeGroupRoleInterface;
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
     * @param NodeInterface  $node
     * @param GroupInterface $group
     * @param string         $role
     * @param string         $accessType
     *
     * @return NodeGroupRoleInterface
     * @throws NodeGroupRoleNotFoundException
     */
    protected function createNodeGroupRole($node, $group, $role, $accessType)
    {
        /** @var $nodeGroupRole NodeGroupRoleInterface */
        $nodeGroupRole = new $this->nodeGroupRoleClass();
        $nodeGroupRole->setNodeId($node->getNodeId());
        $nodeGroupRole->setRole($role);
        $nodeGroupRole->setAccessType($accessType);
        $isGranted = (NodeGroupRoleInterface::ACCESS_DENIED === $accessType) ? false : true;
        if (NodeGroupRoleInterface::ACCESS_INHERIT === $accessType) {
            $parentNodeRole = $group->getNodeRoleByNodeAndRole($node->getParentId(), $role);
            if (null === $parentNodeRole) {
                throw new NodeGroupRoleNotFoundException($role, $node->getNodeId(), $group->getName());
            }
            $isGranted = $parentNodeRole->isGranted();
        }
        $nodeGroupRole->setGranted($isGranted);

        return $nodeGroupRole;
    }
}
