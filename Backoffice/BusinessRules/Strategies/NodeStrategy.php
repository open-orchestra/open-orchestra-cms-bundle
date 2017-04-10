<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * class NodeStrategy
 */
class NodeStrategy extends AbstractBusinessRulesStrategy
{

    CONST DELETE_VERSION = 'DELETE_VERSION';

    protected $nodeRepository;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository
    ) {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return NodeInterface::ENTITY_TYPE;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return array(
            BusinessActionInterface::DELETE => 'canDelete',
            self::DELETE_VERSION => 'canDeleteVersion',
            BusinessActionInterface::EDIT => 'canEdit',
        );
    }

    /**
     * @param NodeInterface $node
     * @param array         $parameters
     *
     * @return boolean
     */
    public function canDelete(NodeInterface $node, array $parameters)
    {
        return $node->getNodeId() !== NodeInterface::ROOT_NODE_ID &&
            false === $this->nodeRepository->hasNodeIdWithoutAutoUnpublishToState($node->getNodeId(), $node->getSiteId()) &&
            $this->nodeRepository->countByParentId($node->getNodeId(), $node->getSiteId()) == 0;
    }

    /**
     * @param NodeInterface $node
     * @param array            $parameters
     *
     * @return boolean
     */
    public function canDeleteVersion(NodeInterface $node, array $parameters)
    {
        return $this->nodeRepository->countNotDeletedVersions($node->getNodeId(), $node->getLanguage(), $node->getSiteId()) > 1 && !$node->getStatus()->isPublishedState();
    }

    /**
     * @param NodeInterface $node
     * @param array         $parameters
     *
     * @return boolean
     */
    public function canEdit(NodeInterface $node, array $parameters)
    {
        return !$node->getStatus()->isBlockedEdition();
    }
}
