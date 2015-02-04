<?php

namespace PHPOrchestra\LogBundle\Processor;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class LogUserProcessor
 */
class LogUserProcessor
{
    protected $requestStack;
    protected $securityContext;

    /**
     * @param SecurityContextInterface $securityContext
     * @param RequestStack             $requestStack
     */
    public function __construct(SecurityContextInterface $securityContext, RequestStack $requestStack)
    {
        $this->securityContext = $securityContext;
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
            $record['extra']['user_name'] = $this->securityContext->getToken()->getUsername();
        }

        return $record;
    }
}
