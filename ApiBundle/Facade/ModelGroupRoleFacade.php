<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class ModelGroupRoleFacade
 */
class ModelGroupRoleFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $type;

    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("string")
     */
    public $modelId;

    /**
     * @Serializer\Type("string")
     */
    public $accessType;
}
