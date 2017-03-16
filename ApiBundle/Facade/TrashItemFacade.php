<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class TrashItemFacade
 */
class TrashItemFacade extends AbstractFacade
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
    public $entityId;

    /**
     * @Serializer\Type("string")
     */
    public $siteId;

    /**
     * @Serializer\Type("DateTime")
     */
    public $deletedAt;
}
