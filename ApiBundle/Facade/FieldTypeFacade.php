<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

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

    public $defaultValue;

    /**
     * @Serializer\Type("boolean")
     */
    public $searchable;

    /**
     * @Serializer\Type("boolean")
     */
    public $listable;

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
