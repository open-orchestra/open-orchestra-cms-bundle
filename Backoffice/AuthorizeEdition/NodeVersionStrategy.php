<?php

namespace OpenOrchestra\Backoffice\AuthorizeEdition;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

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
        return $document instanceof NodeInterface;
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
