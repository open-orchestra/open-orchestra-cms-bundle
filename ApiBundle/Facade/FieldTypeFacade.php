<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class FieldTypeFacade
 */
class FieldTypeFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $fieldId;

    /**
     * @Serializer\Type("string")
     */
    public $label;

    /**
     * @Serializer\Type("string")
     */
    public $defaultValue;

    /**
     * @Serializer\Type("boolean")
     */
    public $searchable;

    /**
     * @var@Serializer\Type("string")
     */
    public $type;

    /**
     * @Serializer\Type("string")
     */
    public $symfonyType;

    /**
     * @Serializer\Type("array<string,string>")
     */
    public $options = array();

    /**
     * @param string $key
     * @param string $value
     */
    public function addOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
