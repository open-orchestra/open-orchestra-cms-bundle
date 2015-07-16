<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber\DataTransformer;

/**
 * Class IntegerToHtmlStringTransformer
 */
class IntegerToHtmlStringTransformer implements ValueTransformerInterface
{
    /**
     * @param integer|double $data
     *
     * @return string
     */
    public function transform($data)
    {
       return "$data";
    }

    /**
     * @param string $fieldType
     * @param mixed  $value
     *
     * @return bool
     */
    public function support($fieldType, $value)
    {
        return gettype($value) == 'integer' && $fieldType == 'integer';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'integer';
    }
}
