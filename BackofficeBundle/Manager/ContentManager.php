<?php

namespace OpenOrchestra\BackofficeBundle\Manager;

use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Manager\VersionableSaverInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ContentManager
 */
class ContentManager
{
    protected $contentTypeRepository;
    protected $contextManager;
    protected $contentClass;
    protected $versionableSaver;
    protected $eventDispatcher;

    /**
     * @param ContextManager                 $contextManager
     * @param string                         $contentClass
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param VersionableSaverInterface      $versionableSaver
     * @param ContentRepositoryInterface     $contentRepository
     * @param EventDispatcherInterface       $eventDispatcher
     */
    public function __construct(
        ContextManager $contextManager,
        $contentClass,
        ContentTypeRepositoryInterface $contentTypeRepository,
        VersionableSaverInterface $versionableSaver,
        ContentRepositoryInterface $contentRepository,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contextManager = $contextManager;
        $this->contentClass = $contentClass;
        $this->versionableSaver = $versionableSaver;
        $this->contentRepository = $contentRepository;
        $this->eventDispatcher = $eventDispatcher;
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

    /**
     * @param ContentInterface $content
     */
    public function restoreContent(ContentInterface $content)
    {
        $contents = $this->contentRepository->findByContentId($content->getContentId());
        /** @var ContentInterface $content */
        foreach ($contents as $content) {
            $content->setDeleted(false);
        }
        $this->eventDispatcher->dispatch(ContentEvents::CONTENT_RESTORE, new ContentEvent($content));
    }
}
