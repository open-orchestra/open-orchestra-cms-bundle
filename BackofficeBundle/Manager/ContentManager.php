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
        if ($language === null) {
            $language = $this->contextManager->getCurrentLocale();
        }

        $content = $this->contentRepository->findOneByContentIdAndLanguage($contentId, $language);

        if($content === null){
            $contentSource = $this->contentRepository->findOneByContentId($contentId);
            if($contentSource !== null){
                $content = clone $contentSource;
                $content->setVersion(1);
                $content->setStatus(null);
                $content->setLanguage($language);
            }
        }

        return $content;
    }
}
