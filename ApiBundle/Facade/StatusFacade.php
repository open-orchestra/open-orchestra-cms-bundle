<?php

namespace OpenOrchestra\ApiBundle\Facade;

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
     * @Serializer\Type("boolean")
     */
    public $initial;

    /**
     * @Serializer\Type("string")
     */
    public $label;

    /**
     * @Serializer\Type("string")
     */
    public $fromRole;

    /**
     * @Serializer\Type("string")
     */
    public $toRole;

    /**
     * @Serializer\Type("string")
     */
    public $displayColor;

    /**
     * @Serializer\Type("string")
     */
    public $codeColor;

    /**
     * @Serializer\Type("boolean")
     */
    public $allowed;
}
