<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class BlockFacade
 */
class BlockFacade extends AbstractFacade
{
    const GENERATE = 'generate';
    const LOAD = 'load';

    /**
     * @Serializer\Type("string")
     */
    public $id;

    /**
     * @Serializer\Type("string")
     */
    public $method;

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
     * @Serializer\Type("OpenOrchestra\ApiBundle\Facade\UiModelFacade")
     */
    public $uiModel;

    /**
     * @Serializer\Type("boolean")
     */
    public $isDeletable = true;

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
