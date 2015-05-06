<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

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
