<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class StatusFacade
 */
class StatusFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("boolean")
     */
    public $published;

    /**
     * @Serializer\Type("string")
     */
    public $label;

    /**
     * @Serializer\Type("string")
     */
    public $role;
}
