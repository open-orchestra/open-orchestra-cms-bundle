<?php

namespace OpenOrchestra\Backoffice\Exception;

/**
 * Class ValueTransfomationFailedException
 */
class ValueTransfomationFailedException extends \Exception
{
    /**
     * @param string $fieldType
     */
    public function __construct($fieldType)
    {
        parent::__construct(sprintf('The field %s has not been correctly transformed', $fieldType));
    }
}
