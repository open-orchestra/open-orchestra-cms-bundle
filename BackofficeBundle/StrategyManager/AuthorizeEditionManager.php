<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\AuthorizeEdition\AuthorizeEditionInterface;

/**
 * Class AuthorizeEditionManager
 *
 * @deprecated use the AuthorizationChecker instead, will be removed in 1.2.0
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

    /**
     * @param mixed $document
     *
     * @return bool
     */
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
