<?php

namespace PHPOrchestra\BackofficeBundle\Exception;

class DisplayBlockIconNotFoundException extends \Exception
{
    /**
     * @param string $message
     */
    public function __construct($message = "")
    {
        parent::__construct('Icon not found for this block type : ' . $message);
    }
}
