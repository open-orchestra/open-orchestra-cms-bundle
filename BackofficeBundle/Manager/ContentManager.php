<?php

namespace OpenOrchestra\BackofficeBundle\Manager;

use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\ModelInterface\Manager\VersionableSaverInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;

/**
 * Class ContentManager
 */
class ContentManager
{
    protected $contentTypeRepository;
    protected $contextManager;
    protected $contentClass;
    protected $versionableSaver;

    /**
     * @param ContextManager                 $contextManager
     * @param string                         $contentClass
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param VersionableSaverInterface      $versionableSaver
     */
    public function __construct(ContextManager $contextManager, $contentClass, ContentTypeRepositoryInterface $contentTypeRepository, VersionableSaverInterface $versionableSaver)
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contextManager = $contextManager;
        $this->contentClass = $contentClass;
        $this->versionableSaver = $versionableSaver;
    }

    /**
     * @param ContentInterface $contentSource
     * @param string           $language
     *
     * @return ContentInterface
     */
    public function createNewLanguageContent($contentSource, $language)
    {
        $content = $this->duplicateContent($contentSource);
        $content->setLanguage($language);

        return $content;
    }

    /**
     * @param string $contentType
     *
     * @return ContentInterface
     */
    public function initializeNewContent($contentType)
    {
        $contentClass = $this->contentClass;
        /** @var ContentInterface $content */
        $content = new $contentClass();
        $content->setLanguage($this->contextManager->getDefaultLocale());
        $content->setSiteId($this->contextManager->getCurrentSiteId());
        $content->setContentType($contentType);

        $contentType = $this->contentTypeRepository->findOneByContentTypeIdInLastVersion($contentType);
        $content->setLinkedToSite($contentType->isLinkedToSite());

        return $content;
    }

    /**
     * Duplicate a content
     *
     * @param ContentInterface $content
     * @param ContentInterface $lastContent
     *
     * @return ContentInterface
     */
    public function duplicateContent(ContentInterface $content, ContentInterface $lastContent = null)
    {
        $lastVersion = $lastContent !== null ? $lastContent->getVersion() : 0;
        $newContent = clone $content;
        $newContent->setVersion($lastVersion + 1);
        $newContent->setStatus(null);
        foreach ($content->getKeywords() as $keyword) {
            $newKeyword = clone $keyword;
            $newContent->addKeyword($newKeyword);
        }
        foreach ($content->getAttributes() as $attribute) {
            $newAttribute = clone $attribute;
            $newContent->addAttribute($newAttribute);
        }

        $this->versionableSaver->saveDuplicated($newContent);

        return $newContent;
    }
}
