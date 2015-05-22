<?php

namespace OpenOrchestra\BackofficeBundle\Manager;

use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class ContentManager
 */
class ContentManager
{
    protected $contextManager;
    protected $contentClass;

    /**
     * @param ContextManager $contextManager
     * @param string         $contentClass
     */
    public function __construct(ContextManager $contextManager, $contentClass)
    {
        $this->contextManager = $contextManager;
        $this->contentClass = $contentClass;
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
        $content->setVersion(1);
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
        $content->setContentType($contentType);

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
        foreach ($content->getKeywords() as $keyword) {
            $newKeyword = clone $keyword;
            $newContent->addKeyword($newKeyword);
        }
        foreach ($content->getAttributes() as $attribute) {
            $newAttribute = clone $attribute;
            $newContent->addAttribute($newAttribute);
        }


        return $newContent;
    }


}
