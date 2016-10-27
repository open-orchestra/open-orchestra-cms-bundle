<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class AbstractEditionRoleController
 */
abstract class AbstractEditionRoleController extends AbstractAdminController
{
    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function getAccessRole(NodeInterface $node)
    {
        if (NodeInterface::TYPE_ERROR === $node->getNodeType()) {
            return TreeNodesPanelStrategy::ROLE_ACCESS_ERROR_NODE;
        }

        return TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE;
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function getEditionRole(NodeInterface $node)
    {
        if (NodeInterface::TYPE_ERROR === $node->getNodeType()) {
            return TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_ERROR_NODE;
        }

        return TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE;
    }
}
