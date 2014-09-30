<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class UiModelFacade
 */
class UiModelFacade implements FacadeInterface
{
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
    public $id;

    /**
     * @Serializer\Type("string")
     */
    public $html;
}
