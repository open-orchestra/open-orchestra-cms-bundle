<?php

namespace OpenOrchestra\ApiBundle\Exceptions\HttpException;

/**
 * Class BadClientCredentialsHttpException
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class BadClientCredentialsHttpException extends ApiException
{
    const DEVELOPER_MESSAGE  = 'client.bad_credentials';
    const HUMAN_MESSAGE      = 'open_orchestra_api.client.bad_credentials';
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
