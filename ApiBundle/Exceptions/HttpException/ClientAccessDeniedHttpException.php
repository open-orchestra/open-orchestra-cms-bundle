<?php

namespace OpenOrchestra\ApiBundle\Exceptions\HttpException;

/**
 * Class ClientAccessDeniedHttpException
 */
class ClientAccessDeniedHttpException extends ApiException
{
    const DEVELOPER_MESSAGE  = 'client.access_denied';
    const HUMAN_MESSAGE      = 'api.exception.client_access_denied';
    const STATUS_CODE        = '404';
    const ERROR_CODE         = 'x';
    const ERROR_SUPPORT      = 'global_platform_main_contact_contact';
    const REDIRECTION        = 'logout';

    /**
     * Constructor
     */
    public function __construct()
    {
        $developerMessage   = self::DEVELOPER_MESSAGE;
        $humanMessage       = self::HUMAN_MESSAGE;
        $statusCode         = self::STATUS_CODE;
        $errorCode          = self::ERROR_CODE;
        $errorSupport       = self::ERROR_SUPPORT;
        $redirection        = self::REDIRECTION;

        parent::__construct($statusCode, $errorCode, $errorSupport, $developerMessage, $humanMessage, $redirection);
    }
}
