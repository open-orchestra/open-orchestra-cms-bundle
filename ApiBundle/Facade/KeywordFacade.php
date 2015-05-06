<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class KeywordFacade
 */
class KeywordFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $label;
}
