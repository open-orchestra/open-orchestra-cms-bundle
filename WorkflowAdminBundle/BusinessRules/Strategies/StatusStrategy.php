<?php

namespace OpenOrchestra\WorkflowAdminBundle\BusinessRules\Strategies;

use OpenOrchestra\Backoffice\BusinessRules\Strategies\AbstractBusinessRulesStrategy;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\UsageFinder\StatusUsageFinder;

/**
 * class StatusStrategy
 */
class StatusStrategy extends AbstractBusinessRulesStrategy
{
    protected $statusUsageFinder;

    /**
     * @param StatusUsageFinder $statusUsageFinder
     */
    public function __construct(StatusUsageFinder $statusUsageFinder)
    {
        $this->statusUsageFinder = $statusUsageFinder;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return StatusInterface::ENTITY_TYPE;
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
     * @param StatusInterface $status
     * @param array           $parameters
     *
     * @return boolean
     */
    public function canDelete(StatusInterface $status, array $parameters)
    {
        return !$this->statusUsageFinder->hasUsage($status)
            && !$status->isInitialState()
            && !$status->isPublishedState()
            && !$status->isTranslationState()
            && !$status->isAutoPublishFromState()
            && !$status->isAutoUnpublishToState();
    }
}
