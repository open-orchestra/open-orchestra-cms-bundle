<?php

namespace OpenOrchestra\Backoffice\ValueTransformer\Strategies;

use OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerInterface;

/**
 * Class DocumentToHtmlStringTransformer
 */
class DocumentToHtmlStringTransformer implements ValueTransformerInterface
{
    protected $property;

    /**
     * @param string $property
     */
    public function __construct($property)
    {
        $this->property = $property;
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function transform($data)
    {
        return $data[$this->property];
    }

    /**
     * @param string $fieldType
     * @param mixed  $value
     *
     * @return bool
     */
    public function support($fieldType, $value)
    {
        return $fieldType === 'document';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'document';
    }
}
