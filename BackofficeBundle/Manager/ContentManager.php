<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\Backoffice\Context\ContextManager;
use PHPOrchestra\ModelInterface\Model\ContentInterface;
use PHPOrchestra\ModelInterface\Repository\ContentRepositoryInterface;

/**
 * Class ContentManager
 */
class ContentManager
{
    protected $contentRepository;
    protected $contextManager;

    /**
     * @param ContentRepositoryInterface $contentRepository
     * @param ContextManager             $contextManager
     */
    public function __construct(ContentRepositoryInterface $contentRepository, ContextManager $contextManager)
    {
        $this->contentRepository = $contentRepository;
        $this->contextManager = $contextManager;
    }
    /**
     * @param string $contentId
     * @param string $language
     *
     * @return ContentInterface
     */
    public function createNewLanguageContent($contentId, $language = null)
    {
        if (is_null($language)) {
            $language = $this->contextManager->getCurrentLocale();
        }

        $content = $this->contentRepository->findOneByContentIdAndLanguage($contentId, $language);

        if (is_null($content)) {
            $contentSource = $this->contentRepository->findOneByContentId($contentId);
            if (!is_null($contentSource)) {
                $content = $this->duplicateContent($contentSource);
                $content->setVersion(1);
                $content->setLanguage($language);
            }
        }

        return $content;
    }

    /**
     * Duplicate a content
     *
     * @param ContentInterface $content
     *
     * @return ContentInterface
     */
    public function duplicateContent(ContentInterface $content)
    {
        $newContent = clone $content;
        $newContent->setVersion($content->getVersion() + 1);
        $newContent->setStatus(null);

        return $newContent;
    }


}
