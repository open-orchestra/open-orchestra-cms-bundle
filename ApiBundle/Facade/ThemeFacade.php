<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class ThemeFacade
 */
class ThemeFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $name;
}
