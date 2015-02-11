<?php

namespace PHPOrchestra\LogBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use PHPOrchestra\ApiBundle\Facade\AbstractFacade;

/**
 * Class LogFacade
 */
class LogFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $id;

    /**
     * @Serializer\Type("integer")
     */
    public $level;

    /**
     * @Serializer\Type("string")
     */
    public $channel;

    /**
     * @Serializer\Type("string")
     */
    public $userIp;

    /**
     * @Serializer\Type("string")
     */
    public $userName;

    /**
     * @Serializer\Type("string")
     */
    public $dateTime;

    /**
     * @Serializer\Type("string")
     */
    public $message;

    /**
     * @Serializer\Type("string")
     */
    public $levelName;

    /**
     * @Serializer\Type("string")
     */
    public $siteName;
}
