<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class WidgetFacade
 */
class WidgetFacade implements FacadeInterface
{
    /**
     * @Serializer\Type("string")
     */
    public $type;
}
