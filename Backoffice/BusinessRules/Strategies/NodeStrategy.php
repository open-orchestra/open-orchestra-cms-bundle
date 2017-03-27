<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

use  OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;

/**
 * class NodeStrategy
 */
class NodeStrategy extends AbstractBusinessRulesStrategy
{

    CONST DELETE_VERSION = 'DELETE_VERSION';

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param ContextManager          $contextManager
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        ContextManager $contextManager
    ) {
        $this->nodeRepository = $nodeRepository;
        $this->contextManager = $contextManager;
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
            ContributionActionInterface::DELETE => 'canDelete',
            self::DELETE_VERSION => 'canDeleteVersion',
            ContributionActionInterface::EDIT => 'canEdit',
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
        $siteId = $this->contextManager->getCurrentSiteId();

        return $node->getNodeId() !== NodeInterface::ROOT_NODE_ID &&
            false === $this->nodeRepository->hasNodeIdWithoutAutoUnpublishToState($node->getNodeId(), $siteId) &&
            $this->nodeRepository->countByParentId($node->getNodeId(), $siteId) == 0;
    }

    /**
     * @param NodeInterface $node
     * @param array            $parameters
     *
     * @return boolean
     */
    public function canDeleteVersion(NodeInterface $node, array $parameters)
    {
        return !$node->getStatus()->isPublishedState();
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
