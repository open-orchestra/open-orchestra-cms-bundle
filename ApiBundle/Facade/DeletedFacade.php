<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\ApiBundle\Facade\Traits\BaseFacade;
use OpenOrchestra\ApiBundle\Facade\Traits\TimestampableFacade;

/**
 * Class DeletedFacade
 */
abstract class DeletedFacade implements FacadeInterface
{
    use TimestampableFacade;
    use BaseFacade;

    /**
     * @Serializer\Type("boolean")
     */
    public $deleted;

    /**
     * @Serializer\Type("string")
     */
    public $name;
}
