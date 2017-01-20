<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class BlockComponentFacade
 */
class BlockComponentFacade implements FacadeInterface
{
    /**
     * @Serializer\Type("string")
     */
    public $component;

    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("string")
     */
    public $description;

    /**
     * @Serializer\Type("array<string,string>")
     */
    public $category = array();
}
