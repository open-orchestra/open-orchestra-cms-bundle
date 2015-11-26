<?php

namespace OpenOrchestra\Backoffice\AuthorizeEdition;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class NodeEditionRoleStrategy
 *
 * @deprecated use the AuthorizationChecker instead, will be removed in 1.2.0
 */
class NodeEditionRoleStrategy implements AuthorizeEditionInterface
{
    protected $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param mixed $document
     *
     * @return bool
     */
    public function support($document)
    {
        return $document instanceof NodeInterface && $document->getNodeType() !== NodeInterface::TYPE_TRANSVERSE && !is_null($document->getId());
    }

    /**
     * @param mixed $document
     *
     * @return bool
     */
    public function isEditable($document)
    {
        return $this->authorizationChecker->isGranted(TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, $document);
    }
}
