<?php

namespace OpenOrchestra\ApiBundle\Exceptions\HttpException;


/**
 * Class AuthorizationNonSupportedHttpException
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class AuthorizationNonSupportedHttpException extends ApiException
{
    const DEVELOPER_MESSAGE  = 'authorization_non_supported';
    const HUMAN_MESSAGE      = 'open_orchestra_api.authorization_non_supported';
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
