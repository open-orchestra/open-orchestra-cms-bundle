<?php

namespace OpenOrchestra\ApiBundle\Facade\OAuth2;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\ApiBundle\Facade\AbstractFacade;

/**
 * Class AccessTokenFacade
 */
class AccessTokenFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $accessToken;

    /**
     * @Serializer\Type("DateTime<'d-m-Y H:i:s'>")
     */
    public $expiresIn;
}
