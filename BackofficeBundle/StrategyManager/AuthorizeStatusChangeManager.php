<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\AuthorizeStatusChange\AuthorizeStatusChangeInterface;
use OpenOrchestra\ModelInterface\Event\StatusableEvent;

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
     * @param StatusableEvent $event
     *
     * @return bool
     */
    public function isGranted(StatusableEvent $statusableEvent)
    {
        foreach ($this->strategies as $strategy) {
            if(!$strategy->isGranted($statusableEvent)) {
                return;
            }
        }
        $document = $statusableEvent->getStatusableElement();
        $toStatus = $statusableEvent->getToStatus();
        $document->setStatus($toStatus);
    }
}
