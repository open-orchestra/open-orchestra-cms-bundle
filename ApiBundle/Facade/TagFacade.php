<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class TagFacade
 */
class TagFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $label;
}
