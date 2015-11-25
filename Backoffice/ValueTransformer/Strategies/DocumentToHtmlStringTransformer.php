<?php

namespace OpenOrchestra\Backoffice\ValueTransformer\Strategies;

use OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerInterface;

/**
 * Class DocumentToHtmlStringTransformer
 */
class DocumentToHtmlStringTransformer implements ValueTransformerInterface
{
    protected $method;

    /**
     * @param string        $method
     */
    public function __construct($method)
    {
        $this->method = $method;
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function transform($data)
    {
        $document = unserialize($data);
        $method = $this->method;

        return $document->$method();
    }

    /**
     * @param string $fieldType
     * @param mixed  $value
     *
     * @return bool
     */
    public function support($fieldType, $value)
    {
        return $fieldType == 'document';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'document';
    }
}
