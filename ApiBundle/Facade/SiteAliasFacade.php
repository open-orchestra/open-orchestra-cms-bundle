<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class SiteAliasFacade
 */
class SiteAliasFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $domain;

    /**
     * @Serializer\Type("string")
     */
    public $language;

    /**
     * @Serializer\Type("string")
     */
    public $prefix;

    /**
     * @Serializer\Type("string")
     */
    public $scheme;
}
