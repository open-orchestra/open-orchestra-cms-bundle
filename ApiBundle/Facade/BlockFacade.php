<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class BlockFacade
 */
class BlockFacade implements FacadeInterface
{
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
}
