<?php

namespace OpenOrchestra\Backoffice\ValueTransformer;

use OpenOrchestra\Backoffice\Exception\ValueTransfomationFailedException;

/**
 * Class ValueTransformerManager
 */
class ValueTransformerManager
{
    protected $transformers = array();

    /**
     * @param ValueTransformerInterface $valueTransformer
     */
    public function addStrategy(ValueTransformerInterface $valueTransformer)
    {
        $this->transformers[$valueTransformer->getName()] = $valueTransformer;
    }

    /**
     * @param string $fieldType
     * @param mixed  $value
     *
     * @throws ValueTransfomationFailedException
     *
     * @return string $valueTransformer
     */
    public function transform($fieldType, $value)
    {
        foreach ($this->transformers as $transformer){
            if ($transformer->support($fieldType, $value)) {
                $value = $transformer->transform($value);
            }
        }

        if (is_string($value)) {
            return $value;
        }

        throw new ValueTransfomationFailedException($fieldType);
    }
}
