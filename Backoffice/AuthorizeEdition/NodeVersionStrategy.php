<?php

namespace OpenOrchestra\Backoffice\AuthorizeEdition;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GeneralNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class NodeVersionStrategy
 */
class NodeVersionStrategy implements AuthorizeEditionInterface
{
    /**
     * @var NodeRepositoryInterface
     */
    protected $nodeRepository;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $autorizationChecker;

    /**
     * @param NodeRepositoryInterface       $nodeRepository
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->nodeRepository = $nodeRepository;
        $this->autorizationChecker = $authorizationChecker;
    }

    /**
     * @param mixed $document
     *
     * @return bool
     */
    public function support($document)
    {
        return $document instanceof NodeInterface;
    }

    /**
     * @param NodeInterface|mixed $document
     *
     * @return bool
     */
    public function isEditable($document)
    {
        $isTransverse = $document->getNodeType() === NodeInterface::TYPE_TRANSVERSE;
        if ((!$isTransverse && !$this->autorizationChecker->isGranted(TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE)) ||
            ($isTransverse && !$this->autorizationChecker->isGranted(GeneralNodesPanelStrategy::ROLE_ACCESS_UPDATE_GENERAL_NODE))
        ) {
            return false;
        }

        $lastVersionNode = $this->nodeRepository->findInLastVersion(
            $document->getNodeId(),
            $document->getLanguage(),
            $document->getSiteId()
        );

        if ($lastVersionNode instanceof NodeInterface) {
            return $document->getVersion() >= $lastVersionNode->getVersion();
        }

        return true;
    }
}
