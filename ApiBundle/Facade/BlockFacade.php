<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class BlockFacade
 */
class BlockFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $id;

    /**
     * @Serializer\Type("string")
     */
    public $component;

    /**
     * @Serializer\Type("boolean")
     */
    public $transverse;

    /**
     * @Serializer\Type("string")
     */
    public $label;

    /**
     * @Serializer\Type("string")
     */
    public $class;

    /**
     * @Serializer\Type("string")
     */
    public $previewContent;

    /**
     * @Serializer\Type("array<string,string>")
     */
    protected $attributes = array();

    /**
     * @param string $key
     * @param string $value
     */
    public function addAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
