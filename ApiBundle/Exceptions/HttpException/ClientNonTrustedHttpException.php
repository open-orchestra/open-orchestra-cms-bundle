<?php

namespace OpenOrchestra\ApiBundle\Exceptions\HttpException;

/**
 * Class ClientNonTrustedHttpException
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class ClientNonTrustedHttpException extends ApiException
{
    const DEVELOPER_MESSAGE  = 'client.non_trusted';
    const HUMAN_MESSAGE      = 'open_orchestra_api.client.client_non_trusted';
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
