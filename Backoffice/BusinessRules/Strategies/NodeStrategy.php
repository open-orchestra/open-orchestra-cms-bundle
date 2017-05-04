<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * class NodeStrategy
 */
class NodeStrategy extends AbstractBusinessRulesStrategy
{

    CONST DELETE_VERSION = 'DELETE_VERSION';
    CONST CHANGE_TO_PUBLISH_STATUS  = 'CHANGE_TO_PUBLISH_STATUS';
    CONST CHANGE_STATUS  = 'CHANGE_STATUS';

    protected $nodeRepository;
    protected $generateFormManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param GenerateFormManager     $generateFormManager
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        GenerateFormManager $generateFormManager
    ) {
        $this->nodeRepository = $nodeRepository;
        $this->generateFormManager = $generateFormManager;
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
            self::CHANGE_TO_PUBLISH_STATUS => 'canChangeToPublishStatus',
            self::CHANGE_STATUS => 'canChangeStatus',
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

    /**
     * @param NodeInterface $node
     * @param array         $parameters
     *
     * @return boolean
     */
    public function canChangeStatus(NodeInterface $node, array $parameters)
    {
        if ($node->getStatus() instanceof StatusInterface && $node->getStatus()->isPublishedState()) {
            return $this->canChangeToPublishStatus($node, $parameters);
        }

        return true;
    }

    /**
     * @param NodeInterface $node
     * @param array         $parameters
     *
     * @return boolean
     */
    public function canChangeToPublishStatus(NodeInterface $node, array $parameters)
    {
        $areas = $node->getAreas();
        $routePattern = $node->getRoutePattern();
        foreach ($areas as $area) {
            $blocks = $area->getBlocks();
            foreach ($blocks as $block) {
                $parameters = $this->generateFormManager->getRequiredUriParameter($block);
                foreach ($parameters as $parameter) {
                    if (false === strpos($routePattern, '{' . $parameter . '}')) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
