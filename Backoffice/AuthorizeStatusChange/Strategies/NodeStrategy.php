<?php

namespace OpenOrchestra\Backoffice\AuthorizeStatusChange\Strategies;

use OpenOrchestra\Backoffice\AuthorizeStatusChange\AuthorizeStatusChangeInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class NodeStrategy
 */
class NodeStrategy implements AuthorizeStatusChangeInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param StatusableInterface $document
     * @param StatusInterface $toStatus
     *
     * @return bool
     */
    public function isGranted(StatusableInterface $document, StatusInterface $toStatus)
    {
        if ($document instanceof NodeInterface) {
            if (!$this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $document)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "node";
    }
}
