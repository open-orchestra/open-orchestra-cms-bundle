<?php

namespace OpenOrchestra\ApiBundle\Exceptions\HttpException;

/**
 * Class ClientBlockedHttpException
 */
class ClientBlockedHttpException extends ApiException
{
    const DEVELOPER_MESSAGE  = 'client.blocked';
    const HUMAN_MESSAGE      = 'open_orchestra_api.client.client_blocked';
    const STATUS_CODE        = '404';
    const ERROR_CODE         = 'x';

    /**
     * Constructor
     */
    public function __construct()
    {
        $developerMessage   = self::DEVELOPER_MESSAGE;
        $humanMessage       = self::HUMAN_MESSAGE;
        $statusCode         = self::STATUS_CODE;
        $errorCode          = self::ERROR_CODE;

        parent::__construct($statusCode, $errorCode, $developerMessage, $humanMessage);
    }
}
