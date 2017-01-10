<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;
use OpenOrchestra\BaseApi\Facade\Traits\TimestampableFacade;

/**
 * Class BlockFacade
 */
class BlockFacade extends AbstractFacade
{
    use TimestampableFacade;

    /**
     * @Serializer\Type("string")
     */
    public $id;

    /**
     * @Serializer\Type("string")
     */
    public $component;

    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("array<string,string>")
     */
    public $category = array();

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
    public $previewContent;

    /**
     * @Serializer\Type("integer")
     */
    public $use;

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
