<?php

namespace OpenOrchestra\Backoffice\ValueTransformer\Strategies;

use OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerInterface;

/**
 * Class EmbeddedEntityToHtmlStringTransformer
 */
class EmbeddedEntityToHtmlStringTransformer implements ValueTransformerInterface
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
        return preg_match('/embedded_.*/', $fieldType);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'embedded_entity';
    }
}
