<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class ContentTypeFacade
 */
class ContentTypeFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $contentTypeId;

    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("int")
     */
    public $version;

    /**
     * @Serializer\Type("string")
     */
    public $status;

    /**
     * @Serializer\Type("array<string>")
     */
    protected $fields = array();

    /**
     * @param string $key
     * @param string $value
     */
    public function addField($key, $value)
    {
        $this->fields[$key] = $value;
    }
}
