<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\AuthorizeStatusChange\AuthorizeStatusChangeInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Class AuthorizeStatusChangeManager
 */
class AuthorizeStatusChangeManager
{
    protected $strategies = array();

    /**
     * @param AuthorizeStatusChangeInterface $strategy
     */
    public function addStrategy(AuthorizeStatusChangeInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * @param StatusableInterface $document
     * @param StatusInterface     $toStatus
     *
     * @return bool
     */
    public function isGranted(StatusableInterface $document, StatusInterface $toStatus)
    {
        foreach ($this->strategies as $strategy) {
            if (!$strategy->isGranted($document, $toStatus)) {
                return false;
            }
        }
        $document->setStatus($toStatus);
    }
}
