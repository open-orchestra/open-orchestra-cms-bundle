<?php

namespace OpenOrchestra\Backoffice\AuthorizeStatusChange\Strategies;

use OpenOrchestra\Backoffice\AuthorizeStatusChange\AuthorizeStatusChangeInterface;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
            if (!$this->authorizationChecker->isGranted(TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE)) {
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
