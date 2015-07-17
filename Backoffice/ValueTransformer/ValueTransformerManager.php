<?php

namespace OpenOrchestra\Backoffice\ValueTransformer;

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
     * @return string $valueTransformer
     */
    public function transform($fieldType, $value)
    {
        foreach ($this->transformers as $transformer){
            if ($transformer->support($fieldType, $value)) {
                $value = $transformer->transform($value);
            }
        }

        return $value;
    }
}
