<?php

namespace OpenOrchestra\ApiBundle\Exceptions;

/**
 * Class TransformerParameterException
 */
class TransformerParameterTypeHttpException extends \Exception
{
    const DEVELOPER_MESSAGE  = 'open_orchestra_api.transformer.type';
    const HUMAN_MESSAGE      = 'open_orchestra_api.transformer.type';
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
