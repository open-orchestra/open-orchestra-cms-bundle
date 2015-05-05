<?php

namespace OpenOrchestra\ApiBundle\Exceptions\HttpException;

/**
 * Class UserNotFoundHttpException
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class UserNotFoundHttpException extends ApiException
{
    const DEVELOPER_MESSAGE  = 'user.not_found';
    const HUMAN_MESSAGE      = 'api.exception.user_not_found';
    const STATUS_CODE        = '404';
    const ERROR_CODE         = 'x';
    const ERROR_SUPPORT      = 'global_platform_main_contact_contact';

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

        parent::__construct($statusCode, $errorCode, $errorSupport, $developerMessage, $humanMessage);
    }
}
