<?php

namespace PHPOrchestra\ApiBundle\Exceptions\HttpException;

/**
 * Class BlockTransformerHttpException
 */
class BlockTransformerHttpException extends ApiException
{
    const DEVELOPER_MESSAGE  = 'php_orchestra_api.block.transformer';
    const HUMAN_MESSAGE      = 'php_orchestra_api.block.transformer';
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
