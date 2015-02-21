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

    /**
     * @param ContextManager $contextManager
     */
    public function __construct(ContextManager $contextManager)
    {
        $this->contextManager = $contextManager;
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
