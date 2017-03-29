<?php

namespace OpenOrchestra\WorkflowAdminBundle\Exceptions\HttpException;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;

/**
 * Class StatusNotDeletableException
 */
class StatusNotDeletableException extends ApiException
{
    const DEVELOPER_MESSAGE  = 'open_orchestra_workflow_admin.status.delete.impossible';
    const HUMAN_MESSAGE      = 'open_orchestra_workflow_admin.status.delete.impossible';
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
