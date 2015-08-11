<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\AuthorizeEdition\AuthorizeEditionInterface;

/**
 * Class AuthorizeEditionManager
 */
class AuthorizeEditionManager
{
    protected $strategies = array();

    /**
     * @param AuthorizeEditionInterface $strategy
     */
    public function addStrategy(AuthorizeEditionInterface $strategy)
    {
        $this->strategies[] = $strategy;
    }

    public function isEditable($document)
    {
        /** @var AuthorizeEditionInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($document)) {
                if (!$strategy->isEditable($document)) {
                    return false;
                }
            }
        }

        return true;
    }
}
