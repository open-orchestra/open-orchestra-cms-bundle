<?php

namespace PHPOrchestra\ApiBundle\Exceptions\HttpException;

use PHPOrchestra\ApiBundle\Exceptions\ApiException as BaseApiAxception;

/**
 * Class ApiException
 */
abstract class ApiException extends BaseApiAxception
{
    protected $statusCode;
    protected $errorCode;
    protected $errorSupport;
    protected $developerMessage;
    protected $humanMessage;
    protected $redirection;

    /**
     * @param string      $statusCode
     * @param int         $errorCode
     * @param string      $errorSupport
     * @param string      $developerMessage
     * @param string      $humanMessage
     * @param string|null $redirection
     */
    public function __construct($statusCode, $errorCode, $errorSupport, $developerMessage, $humanMessage, $redirection = null)
    {
        $this->statusCode        = $statusCode;
        $this->errorCode         = $errorCode;
        $this->errorSupport      = $errorSupport;
        $this->developerMessage  = $developerMessage;
        $this->humanMessage      = $humanMessage;
        $this->redirection       = $redirection;

        parent::__construct($developerMessage);
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getErrorSupport()
    {
        return $this->errorSupport;
    }

    /**
     * @return string
     */
    public function getDeveloperMessage()
    {
        return $this->developerMessage;
    }

    /**
     * @return string
     */
    public function getHumanMessage()
    {
        return $this->humanMessage;
    }

    /**
     * Returns the status code.
     *
     * @return integer An HTTP response status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Returns response headers.
     *
     * @return array Response headers
     */
    public function getHeaders()
    {
        return array();
    }

    /**
     * @return null|string
     */
    public function getRedirection()
    {
        if (null === $this->redirection) {
            return 'logout';
        }

        return $this->redirection;
    }
}
