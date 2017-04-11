<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\BBcodeBundle\Parser\BBcodeParserInterface;
use OpenOrchestra\DisplayBundle\BBcode\InternalLinkDefinition;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;

/**
 * Class AbstractContentReferenceStrategy
 */
abstract class AbstractContentReferenceStrategy implements ReferenceStrategyInterface
{
    protected $contentRepository;
    protected $bbcodeParser;

    /**
     * @param ContentRepositoryInterface $contentRepository
     * @param BBcodeParserInterface      $bbcodeParser
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository,
        BBcodeParserInterface      $bbcodeParser
    ) {
        $this->contentRepository = $contentRepository;
        $this->bbcodeParser = $bbcodeParser;
    }

    /**
     * Recursively extract cont ent ids from elements (bloc, attribute, collection attribute, etc ...)
     *
     * @param mixed $element
     * @param array $references
     *
     * @return array
     */
    protected function extractContentsFromElement($element, array $references = array())
    {
        if ($this->isContentSearchAttribute($element)) {
            $references[] = $element['contentId'];

        } elseif ($this->isEmbeddedContentAttribute($element)) {
            $references[] = $element->getValue()['contentId'];

        } elseif (
            $element instanceof ContentAttributeInterface
            && $element->getType() === 'tinymce'
            && $this->hasInternalLinkBBCode($element->getValue())
        ) {
            $references = array_merge($references, $this->extractContentIdFromBBCode($element->getValue()));

        } elseif (
            is_string($element) &&
            $this->hasInternalLinkBBCode($element)
        ) {
            $references = array_merge($references, $this->extractContentIdFromBBCode($element));

        } elseif (is_array($element)) {
            foreach ($element as $item) {
                $references = array_merge($references, $this->extractContentsFromElement($item));
            }
        }

        return $references;
    }

    /**
     * @param mixed $element
     *
     * @return boolean
     */
    protected function hasInternalLinkBBCode($element)
    {
        $internalLinkBBCode = '/\[' . InternalLinkDefinition::TAG_NAME . '[^]]*].*?\[\/'. InternalLinkDefinition::TAG_NAME . '\]/m';

        return preg_match($internalLinkBBCode, $element) === 1;
    }

    /**
     * @param string $str
     *
     * @return array
     */
    protected function extractContentIdFromBBCode($str)
    {
        $references = array();
        /** @var \OpenOrchestra\BBcodeBundle\Parser\BBcodeParserInterface $parserBBcode */
        $parsedBBcode = $this->bbcodeParser->parse($str);
        $nodeTags = $parsedBBcode->getElementByTagName(InternalLinkDefinition::TAG_NAME);
        /** @var \OpenOrchestra\BBcodeBundle\ElementNode\BBcodeElementNodeInterface $mediaTag */
        foreach ($nodeTags as $nodeTag) {
            $linkAttribute = json_decode(html_entity_decode($nodeTag->getAttribute()['link']), true);
            if (!empty($linkAttribute['contentSearch_contentId'])) {
                $references[] = $linkAttribute['contentSearch_contentId'];
            }
        }

        return $references;
    }

    /**
     * Check if $attributeValue matches with a content search attribute
     *
     * @param mixed $attributeValue
     *
     * @return boolean
     */
    protected function isContentSearchAttribute($attributeValue)
    {
        return is_array($attributeValue)
            && array_key_exists('contentType', $attributeValue)
            && array_key_exists('choiceType', $attributeValue)
            && array_key_exists('keywords', $attributeValue)
            && isset($attributeValue['contentId']);
    }

    /**
     * @param mixed $attributeValue
     *
     * @return bool
     */
    protected function isEmbeddedContentAttribute($attributeValue)
    {
        return $attributeValue instanceof ContentAttributeInterface
            && $attributeValue->getType() === 'embedded_content';
    }
}
