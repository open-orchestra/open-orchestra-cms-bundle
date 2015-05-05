<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\ApiBundle\Facade\Traits\BaseFacade;

/**
 * Class AbstractFacade
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class AbstractFacade implements FacadeInterface
{
    use BaseFacade;
}
