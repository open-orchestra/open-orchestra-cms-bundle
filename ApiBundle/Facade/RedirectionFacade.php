<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class RedirectionFacade
 */
class RedirectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $siteName;

    /**
     * @Serializer\Type("string")
     */
    public $routePattern;

    /**
     * @Serializer\Type("string")
     */
    public $locale;

    /**
     * @Serializer\Type("string")
     */
    public $redirection;

    /**
     * @Serializer\Type("boolean")
     */
    public $permanent;
}
