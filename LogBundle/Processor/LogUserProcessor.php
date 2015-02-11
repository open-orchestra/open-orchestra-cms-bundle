<?php

namespace PHPOrchestra\LogBundle\Processor;

use PHPOrchestra\Backoffice\Context\ContextManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class LogUserProcessor
 */
class LogUserProcessor
{
    protected $context;
    protected $requestStack;
    protected $securityContext;

    /**
     * @param SecurityContextInterface $securityContext
     * @param RequestStack             $requestStack
     * @param ContextManager           $context
     */
    public function __construct(SecurityContextInterface $securityContext, RequestStack $requestStack, ContextManager $context)
    {
        $this->securityContext = $securityContext;
        $this->requestStack = $requestStack;
        $this->context = $context;
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

        $record['extra']['site_name'] = $this->context->getCurrentSiteName();

        return $record;
    }
}
