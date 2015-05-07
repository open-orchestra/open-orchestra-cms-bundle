<?php

namespace OpenOrchestra\MediaAdminBundle\Exceptions\HttpException;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;

/**
 * Class MediaNotDeletableException
 */
class MediaNotDeletableException extends ApiException
{
    const DEVELOPER_MESSAGE  = 'open_orchestra_backoffice.media.delete.impossible';
    const HUMAN_MESSAGE      = 'open_orchestra_backoffice.form.media.delete';
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
