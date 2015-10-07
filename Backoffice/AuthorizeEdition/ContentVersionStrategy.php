<?php

namespace OpenOrchestra\Backoffice\AuthorizeEdition;

use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;

/**
 * Class ContentVersionStrategy
 */
class ContentVersionStrategy implements AuthorizeEditionInterface
{
    protected $contentRepository;

    /**
     * @param ContentRepositoryInterface $contentRepository
     */
    public function __construct(ContentRepositoryInterface $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }

    /**
     * @param mixed $document
     *
     * @return bool
     */
    public function support($document)
    {
        return $document instanceof ContentInterface;
    }

    /**
     * @param mixed|ContentInterface $document
     *
     * @return bool
     */
    public function isEditable($document)
    {
        $lastDocument = $this->contentRepository->findOneByLanguage($document->getContentId(), $document->getLanguage());

        if (!$lastDocument instanceof ContentInterface) {
            return true;
        }

        return $lastDocument->getVersion() <= $document->getVersion();
    }
}
