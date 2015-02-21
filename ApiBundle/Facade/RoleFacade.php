<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class RoleFacade
 */
class RoleFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("string")
     */
    public $fromStatus;

    /**
     * @Serializer\Type("string")
     */
    public $toStatus;
}
