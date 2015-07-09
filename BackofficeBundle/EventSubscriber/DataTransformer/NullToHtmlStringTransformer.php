<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber\DataTransformer;

/**
 * Class NullToHtmlStringTransformer
 */
class NullToHtmlStringTransformer implements ValueTransformerInterface
{
    /**
     * @param null $data
     *
     * @return string
     */
    public function transform($data)
    {
        return "none";
    }

    /**
     * @param string $fieldType
     * @param mixed  $value
     *
     * @return bool
     */
    public function support($fieldType, $value)
    {
        return $value === null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'null';
    }
}
