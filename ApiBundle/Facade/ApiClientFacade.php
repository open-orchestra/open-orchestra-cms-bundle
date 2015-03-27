<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class ApiClientFacade
 */
class ApiClientFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("boolean")
     */
    public $trusted;

    /**
     * @Serializer\Type("string")
     */
    public $key;

    /**
     * @Serializer\Type("string")
     */
    public $secret;
}
