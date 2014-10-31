<?php

namespace PHPOrchestra\ApiBundle\Exceptions\HttpException;

use PHPOrchestra\ApiBundle\Exceptions\HttpException\ApiException;

/**
 * Class FolderNotDeletableException
 */
class FolderNotDeletableException extends ApiException
{
    const DEVELOPER_MESSAGE  = 'Tu peux pas le supprimer';
    const HUMAN_MESSAGE      = 'php_orchestra_backoffice.form.folder.delete';
    const STATUS_CODE        = '404';
    const ERROR_CODE         = 'x';
    const ERROR_SUPPORT      = 'You can t delete it';

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
