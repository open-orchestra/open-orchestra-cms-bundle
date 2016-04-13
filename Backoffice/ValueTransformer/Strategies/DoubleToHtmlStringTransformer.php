<?php

namespace OpenOrchestra\Backoffice\ValueTransformer\Strategies;

use OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerInterface;

/**
 * Class DoubleToHtmlStringTransformer
 */
class DoubleToHtmlStringTransformer implements ValueTransformerInterface
{
    /**
     * @param integer|double $data
     *
     * @return string
     */
    public function transform($data)
    {
       return (string) $data;
    }

    /**
     * @param string $fieldType
     * @param mixed  $value
     *
     * @return bool
     */
    public function support($fieldType, $value)
    {
        return gettype($value) == 'double';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'double';
    }
}
