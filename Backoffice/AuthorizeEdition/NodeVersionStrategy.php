<?php

namespace OpenOrchestra\Backoffice\AuthorizeEdition;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GeneralNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class NodeVersionStrategy
 *
 * @deprecated use the AuthorizationChecker instead, will be removed in 1.2.0
 */
class NodeVersionStrategy implements AuthorizeEditionInterface
{
    protected $nodeRepository;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(NodeRepositoryInterface $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param mixed $document
     *
     * @return bool
     */
    public function support($document)
    {
        return $document instanceof NodeInterface && $document->getNodeType() !== NodeInterface::TYPE_TRANSVERSE;
    }

    /**
     * @param NodeInterface|mixed $document
     *
     * @return bool
     */
    public function isEditable($document)
    {
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
