<?php

namespace OpenOrchestra\Backoffice\Manager;

use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\Backoffice\Util\UniqueIdGenerator;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class ContentManager
 */
class ContentManager
{
    protected $statusRepository;
    protected $contextManager;
    protected $contentClass;
    protected $tokenStorage;

    /**
     * @param StatusRepositoryInterface  $statusRepository
     * @param ContextManager             $contextManager
     * @param string                     $contentClass
     * @param UniqueIdGenerator          $uniqueIdGenerator
     * @param TokenStorage               $tokenStorageManager
     */
    public function __construct(
        StatusRepositoryInterface $statusRepository,
        ContextManager $contextManager,
        $contentClass,
        UniqueIdGenerator $uniqueIdGenerator,
        TokenStorage $tokenStorage
    ) {
        $this->statusRepository = $statusRepository;
        $this->contextManager = $contextManager;
        $this->contentClass = $contentClass;
        $this->uniqueIdGenerator = $uniqueIdGenerator;
        $this->tokenManager = $tokenStorage;
    }

    /**
     * @param string  $contentType
     * @param string  $language
     * @param boolean $isLinkedToSite
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
        $content->setVersion($this->uniqueIdGenerator->generateUniqueId());
        $content->setCreatedBy($this->tokenStorage->getToken()->getUser()->getUsername());

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
        $content = $this->cloneContent($contentSource);
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
        $newContent->setContentId($contentId);
        $newContent->setName($this->duplicateLabel($content->getName()));
        $newContent = $this->setVersionName($newContent);

        return $newContent;
    }

    /**
     * Duplicate a content
     *
     * @param ContentInterface $originalContent
     * @param string           $versionName
     *
     * @return ContentInterface
     */
    public function newVersionContent(ContentInterface $originalContent, $versionName = '')
    {
        $newContent = $this->cloneContent($originalContent);
        $newContent->setVersionName($versionName);
        if (empty($versionName)) {
            $newContent = $this->setVersionName($newContent);
        }

        return $newContent;
    }

    /**
     * @param ContentInterface $node
     *
     * @return ContentInterface
     */
    public function setVersionName(ContentInterface $node)
    {
        $date = new \DateTime("now");
        $versionName = $node->getName().'_'. $date->format("Y-m-d_H:i:s");
        $node->setVersionName($versionName);

        return $node;
    }


    /**
     * @param ContentInterface $content
     *
     * @return ContentInterface
     */
    protected function cloneContent(ContentInterface $content)
    {
        $status = $this->statusRepository->findOneByInitial();

        $newContent = clone $content;
        $newContent->setStatus($status);
        $newContent->setVersion($this->uniqueIdGenerator->generateUniqueId());
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
