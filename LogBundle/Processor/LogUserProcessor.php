<?php

namespace PHPOrchestra\LogBundle\Processor;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class LogUserProcessor
 */
class LogUserProcessor
{
    protected $requestStack;
    protected $session;

    /**
     * @param Session      $session
     * @param RequestStack $requestStack
     */
    public function __construct(Session $session, RequestStack $requestStack)
    {
        $this->session = $session;
        $this->requestStack = $requestStack;
    }

    /**
     * @param array $record
     *
     * @return array
     */
    public function processRecord(array $record)
    {
        $request = $this->requestStack->getCurrentRequest();

        $record['extra']['user_ip'] = '0.0.0.0';
        if (null !== $request) {
            $record['extra']['user_ip'] = $request->getClientIp();
        }

        return $record;
    }
}
