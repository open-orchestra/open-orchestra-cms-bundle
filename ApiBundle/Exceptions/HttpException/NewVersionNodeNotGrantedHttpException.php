<?php

namespace OpenOrchestra\ApiBundle\Exceptions\HttpException;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;

/**
 * Class NewVersionNodeNotGrantedHttpException
 */
class NewVersionNodeNotGrantedHttpException extends ApiException
{
    const DEVELOPER_MESSAGE  = 'open_orchestra_api.node.new_version_not_granted';
    const HUMAN_MESSAGE      = 'open_orchestra_api.node.new_version_not_granted';
    const STATUS_CODE        = '403';
    const ERROR_CODE         = 'new_version_not_granted';

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
