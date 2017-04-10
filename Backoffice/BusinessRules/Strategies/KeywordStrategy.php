<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

use OpenOrchestra\ModelInterface\Model\KeywordInterface;

/**
 * class KeywordStrategy
 */
class KeywordStrategy extends AbstractBusinessRulesStrategy
{
    /**
     * @return string
     */
    public function getType()
    {
        return KeywordInterface::ENTITY_TYPE;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return array(
            BusinessActionInterface::DELETE => 'canDelete',
        );
    }

    /**
     * @param KeywordInterface $keyword
     * @param array            $parameters
     *
     * @return boolean
     */
    public function canDelete(KeywordInterface $keyword, array $parameters)
    {
        return !$keyword->isUsed();
    }
}
