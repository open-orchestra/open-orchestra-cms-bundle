<?php

namespace OpenOrchestra\Backoffice\ValueTransformer\Strategies;

use OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerInterface;

/**
 * Class ObjectToHtmlStringTransformer
 */
class ObjectToHtmlStringTransformer implements ValueTransformerInterface
{
    /**
     * @param mixed $data
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
        return gettype($value) == 'object';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'object';
    }
}
