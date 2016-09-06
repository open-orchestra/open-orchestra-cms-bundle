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

    /**
     * @Serializer\Type("string")
     */
    public $type;

    /**
     * @Serializer\Type("string")
     */
    public $stringValue;

    /**
     * @Serializer\Type("ContentAttributeValue")
     */
    public $value;
}
