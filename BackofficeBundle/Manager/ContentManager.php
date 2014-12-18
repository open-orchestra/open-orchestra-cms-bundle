<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelBundle\Repository\ContentRepository;
use PHPOrchestra\Backoffice\Context\ContextManager;

/**
 * Class ContentManager
 */
class ContentManager
{
    protected $contentRepository;

    protected $contextManager;

    /**
     * Constructor
     *
     * @param ContentRepository $contentRepository
     */
    public function __construct(ContentRepository $contentRepository, ContextManager $contextManager)
    {
        $this->contentRepository = $contentRepository;
        $this->contextManager = $contextManager;
    }
    /**
     * @param ContentInterface $Content
     *
     * @return ContentInterface
     */
    public function createNewLanguageContent($contentId, $language = null)
    {
        if ($language === null) {
            $language = $this->contextManager->getCurrentLocale();
        }

        $content = $this->contentRepository->findOneBy(array('contentId' => $contentId, 'language' => $language));

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
