<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * class BlockStrategy
 */
class BlockStrategy extends AbstractBusinessRulesStrategy
{
    /**
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(NodeRepositoryInterface $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return BlockInterface::ENTITY_TYPE;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return array(
            ContributionActionInterface::DELETE => 'canDelete',
        );
    }

    /**
     * @param BlockInterface $block
     * @param array          $parameters
     *
     * @return boolean
     */
    public function canDelete(BlockInterface $block, array $parameters)
    {
        return !$block->isTransverse() || 0 == $this->nodeRepository->countBlockUsed($block->getId());
    }
}
