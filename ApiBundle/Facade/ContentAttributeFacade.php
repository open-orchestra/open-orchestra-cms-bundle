<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class ContentAttributeFacade
 */
class ContentAttributeFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $name;

    public $value;

    /**
     * @Serializer\Type("string")
     */
    public $stringValue;
}
