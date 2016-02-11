<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class DocumentGroupRoleFacade
 */
class DocumentGroupRoleFacade extends AbstractFacade
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
    public $document;

    /**
     * @Serializer\Type("string")
     */
    public $accessType;
}
