<?php

namespace OpenOrchestra\GroupBundle\EventListener;

use OpenOrchestra\BackofficeBundle\Model\DocumentGroupRoleInterface;
use OpenOrchestra\GroupBundle\Exception\NodeGroupRoleNotFoundException;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use OpenOrchestra\Backoffice\Model\GroupInterface;
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
        $accessType = DocumentGroupRoleInterface::ACCESS_INHERIT;
        if (NodeInterface::ROOT_NODE_ID === $node->getNodeId()) {
            $accessType = DocumentGroupRoleInterface::ACCESS_GRANTED;
        }

        return $accessType;
    }

    /**
     * @param NodeInterface  $node
     * @param GroupInterface $group
     * @param string         $role
     * @param string         $accessType
     *
     * @return DocumentGroupRoleInterface
     * @throws NodeGroupRoleNotFoundException
     */
    protected function createNodeGroupRole($node, $group, $role, $accessType)
    {
        /** @var $nodeGroupRole DocumentGroupRoleInterface */
        $nodeGroupRole = new $this->nodeGroupRoleClass();
        $nodeGroupRole->setType(DocumentGroupRoleInterface::TYPE_NODE);
        $nodeGroupRole->setId($node->getNodeId());
        $nodeGroupRole->setRole($role);
        $nodeGroupRole->setAccessType($accessType);
        $isGranted = (DocumentGroupRoleInterface::ACCESS_DENIED === $accessType) ? false : true;
        if (DocumentGroupRoleInterface::ACCESS_INHERIT === $accessType) {
            $parentNodeRole = $group->getNodeRoleByIdAndRole($node->getParentId(), $role);
            if (null === $parentNodeRole) {
                throw new NodeGroupRoleNotFoundException($role, $node->getParentId(), $group->getName());
            }
            $isGranted = $parentNodeRole->isGranted();
        }
        $nodeGroupRole->setGranted($isGranted);

        return $nodeGroupRole;
    }
}
