<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use OpenOrchestra\BBcodeBundle\Parser\BBcodeParserInterface;
use OpenOrchestra\DisplayBundle\BBcode\InternalLinkDefinition;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class NodeInBlockReferenceStrategy
 */
class NodeInBlockReferenceStrategy implements ReferenceStrategyInterface
{
    protected $currentSiteManager;
    protected $bbcodeParser;
    protected $nodeRepository;

    /**
     * @param ContextBackOfficeInterface $currentSiteManager
     * @param BBcodeParserInterface      $bbcodeParser
     * @param NodeRepositoryInterface    $nodeRepository
     */
    public function __construct(
        ContextBackOfficeInterface $currentSiteManager,
        BBcodeParserInterface $bbcodeParser,
        NodeRepositoryInterface $nodeRepository
    ) {
        $this->currentSiteManager = $currentSiteManager;
        $this->bbcodeParser = $bbcodeParser;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param mixed $entity
     *
     * @return boolean
     */
    public function support($entity)
    {
        return ($entity instanceof BlockInterface);
    }

    /**
     * @param mixed $entity
     */
    public function addReferencesToEntity($entity)
    {
        if ($this->support($entity)) {
            $references = $this->extractNodesFromElement($entity->getAttributes());
            foreach ($references as $nodeReference) {
                $nodeId = $nodeReference['nodeId'];
                $siteId = $nodeReference['siteId'];
                $this->nodeRepository->updateUseReference($entity->getId(), $nodeId, $siteId, BlockInterface::ENTITY_TYPE);
            }
        }
    }

    /**
     * @param mixed $element
     *
     * @return array
     */
    protected function extractNodesFromElement($element)
    {
        $references = array();
        if ($this->isNodeChoiceAttribute($element)) {
            $references = array($this->extractNodeFromChoiceAttribute($element));
        } elseif (is_string($element) && $this->hasInternalLinkBBCode($element)) {
            $references = array_merge($references, $this->extractNodeFromBBCode($element));
        } elseif (is_array($element)) {
            foreach ($element as $item) {
                $references = array_merge($references, $this->extractNodesFromElement($item));
            }
        }
        return $references;
    }

    /**
     * @param mixed $entity
     */
    public function removeReferencesToEntity($entity)
    {
        if ($this->support($entity)) {
            $blockId = $entity->getId();

            $nodeUsedInBlock = $this->nodeRepository->findByUsedInEntity($blockId, BlockInterface::ENTITY_TYPE);
            /** @var NodeInterface $node */
            foreach ($nodeUsedInBlock as $node) {
                $node->removeUseInEntity($blockId, BlockInterface::ENTITY_TYPE);
            }
        }
    }

    /**
     * @param $str
     *
     * @return boolean
     */
    protected function hasInternalLinkBBCode($str)
    {
        $internalLinkBBCode = '/\[' . InternalLinkDefinition::TAG_NAME . '[^]]*].*?\[\/'. InternalLinkDefinition::TAG_NAME . '\]/m';

        return preg_match($internalLinkBBCode, $str) === 1;
    }

    /**
     * @param string $str
     *
     * @return array
     */
    protected function extractNodeFromBBCode($str)
    {
        $references = array();
        /** @var \OpenOrchestra\BBcodeBundle\Parser\BBcodeParserInterface $parserBBcode */
        $parsedBBcode = $this->bbcodeParser->parse($str);
        $nodeTags = $parsedBBcode->getElementByTagName(InternalLinkDefinition::TAG_NAME);
        /** @var \OpenOrchestra\BBcodeBundle\ElementNode\BBcodeElementNodeInterface $mediaTag */
        foreach ($nodeTags as $nodeTag) {
            $linkAttribute = json_decode(html_entity_decode($nodeTag->getAttribute()['link']), true);
            $nodeId = $linkAttribute['site']['nodeId'];
            $siteId = $linkAttribute['site']['siteId'];
            $references[] = array('nodeId' => $nodeId, 'siteId' => $siteId);
        }
        return $references;
    }

    /**
     * @param array $element
     *
     * @return array
     */
    protected function extractNodeFromChoiceAttribute($element)
    {
        $keyNodeId = current(array_intersect(array('contentNodeId', 'nodeName', 'nodeToLink'), array_keys($element)));
        $nodeId = $element[$keyNodeId];
        if (isset($element['siteId'])) {
            $siteId = $element['siteId'];
        } else {
            $siteId = $this->currentSiteManager->getSiteId();
        }
        return array('nodeId' => $nodeId, 'siteId' => $siteId);
    }

    /**
     * Check if $attributeValue matches with a node choice attribute
     *
     * @param mixed $attributeValue
     *
     * @return boolean
     */
    protected function isNodeChoiceAttribute($attributeValue)
    {
        return is_array($attributeValue)
        && (
            array_key_exists('contentNodeId', $attributeValue) ||
            array_key_exists('nodeName', $attributeValue) ||
            array_key_exists('nodeToLink', $attributeValue)
        );
    }
}
