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
        $granted = true;
        foreach ($this->strategies as $strategy) {
            $granted = $granted && $strategy->isGranted($statusableEvent);
        }
        if ($granted) {
            $document = $statusableEvent->getStatusableElement();
            $toStatus = $statusableEvent->getToStatus();
            $document->setStatus($toStatus);
        }
    }
}
