<?php

namespace OpenOrchestra\ApiBundle\Exceptions\HttpException;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;

/**
 * Class NodeNotEditableException
 */
class NodeNotEditableException extends ApiException
{
    const DEVELOPER_MESSAGE  = 'open_orchestra_api.node.edit.impossible';
    const HUMAN_MESSAGE      = 'open_orchestra_api.node.edit.impossible';
    const STATUS_CODE        = '403';
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
