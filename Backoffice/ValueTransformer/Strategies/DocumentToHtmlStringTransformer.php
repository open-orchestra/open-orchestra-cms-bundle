<?php

namespace OpenOrchestra\Backoffice\ValueTransformer\Strategies;

use OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerInterface;

/**
 * Class DocumentToHtmlStringTransformer
 */
class DocumentToHtmlStringTransformer implements ValueTransformerInterface
{
    protected $documentClass;
    protected $method;

    /**
     * @param string        $documentClass
     * @param string        $method
     */
    public function __construct($documentClass, $method)
    {
        $this->documentClass = $documentClass;
        $this->method = $method;
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function transform($data)
    {
        $method = $this->method;

        return $data->$method();
    }

    /**
     * @param string $fieldType
     * @param mixed  $value
     *
     * @return bool
     */
    public function support($fieldType, $value)
    {
        return $fieldType == 'document' && $value instanceof $this->documentClass;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'document';
    }
}
