<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class BlockFacade
 */
class BlockFacade implements FacadeInterface
{
    const GENERATE = 'generate';
    const LOAD = 'load';

    /**
     * @Serializer\Type("string")
     */
    public $method;

    /**
     * @Serializer\Type("string")
     */
    public $component;

    /**
     * @Serializer\Type("PHPOrchestra\ApiBundle\Facade\UiModelFacade")
     */
    public $uiModel;

    /**
     * @Serializer\Type("string")
     */
    public $nodeId;

    /**
     * @Serializer\Type("int")
     */
    public $blockId;

    /**
     * @Serializer\Type("array<string>")
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
