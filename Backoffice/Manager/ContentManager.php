<?php

namespace OpenOrchestra\Backoffice\Manager;

use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\ModelInterface\Saver\VersionableSaverInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;

/**
 * Class ContentManager
 */
class ContentManager
{
    protected $contentTypeRepository;
    protected $statusRepository;
    protected $contextManager;
    protected $versionableSaver;
    protected $contentClass;

    /**
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param StatusRepositoryInterface      $statusRepository
     * @param ContextManager                 $contextManager
     * @param VersionableSaverInterface      $versionableSaver
     * @param string                         $contentClass
     */
    public function __construct(
        ContentTypeRepositoryInterface $contentTypeRepository,
        StatusRepositoryInterface $statusRepository,
        ContextManager $contextManager,
        VersionableSaverInterface $versionableSaver,
        $contentClass
    )
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->statusRepository = $statusRepository;
        $this->contextManager = $contextManager;
        $this->versionableSaver = $versionableSaver;
        $this->contentClass = $contentClass;
    }

    /**
     * @param string $contentType
     * @param string $language
     *
     * @return ContentInterface
     */
    public function initializeNewContent($contentType, $language, $isLinkedToSite)
    {
        $initialStatus = $this->statusRepository->findOneByInitial();

        $contentClass = $this->contentClass;
        /** @var ContentInterface $content */
        $content = new $contentClass();
        $content->setLanguage($language);
        $content->setSiteId($this->contextManager->getCurrentSiteId());
        $content->setContentType($contentType);
        $content->setLinkedToSite($isLinkedToSite);
        $content->setStatus($initialStatus);

        return $content;
    }

    /**
     * @param ContentInterface $contentSource
     * @param string           $language
     *
     * @return ContentInterface
     */
    public function createNewLanguageContent($contentSource, $language)
    {
        $translationStatus = $this->statusRepository->findOneByTranslationState();

        $content = $this->newVersionContent($contentSource);
        $content->setLanguage($language);
        $content->setStatus($translationStatus);

        return $content;
    }

    /**
     * Duplicate a content
     *
     * @param ContentInterface $content
     * @param string|null      $contentId
     *
     * @return ContentInterface
     */
    public function duplicateContent(ContentInterface $content, $contentId = null)
    {
        $newContent = $this->cloneContent($content);
        $newContent->setVersion(1);
        $newContent->setContentId($contentId);
        $newContent->setName($this->duplicateLabel($content->getName()));

        $this->versionableSaver->saveDuplicated($newContent);

        return $newContent;
    }

    /**
     * Duplicate a content
     *
     * @param ContentInterface $content
     * @param ContentInterface $lastContent
     *
     * @return ContentInterface
     */
    public function newVersionContent(ContentInterface $content, ContentInterface $lastContent = null)
    {
        $contentType = $this->contentTypeRepository->findOneByContentTypeIdInLastVersion($content->getContentType());

        $lastVersion = ($contentType->isDefiningVersionable() && $lastContent !== null) ? $lastContent->getVersion() : 0;
        $newContent = $this->cloneContent($content);
        $newContent->setVersion($lastVersion + 1);
        $this->versionableSaver->saveDuplicated($newContent);

        return $newContent;
    }

    /**
     * @param ContentInterface $content
     *
     * @return ContentInterface
     */
    protected function cloneContent(ContentInterface $content)
    {
        $newContent = clone $content;
        $newContent->setCurrentlyPublished(false);
        $newContent->setStatus(null);
        foreach ($content->getKeywords() as $keyword) {
            $newContent->addKeyword($keyword);
        }
        foreach ($content->getAttributes() as $attribute) {
            $newAttribute = clone $attribute;
            $newContent->addAttribute($newAttribute);
        }

        return $newContent;
    }


    /**
     * @param string $label
     *
     * @return string
     */
    protected function duplicateLabel($label)
    {
        $patternNameVersion = '/.*_([0-9]+$)/';
        if (0 !== preg_match_all($patternNameVersion, $label, $matches)) {
            $version = (int) $matches[1][0] + 1;
            return preg_replace('/[0-9]+$/', $version, $label);
        }

        return $label . '_2';
    }
}
