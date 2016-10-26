<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\BBcodeBundle\Parser\BBcodeParserInterface;
use OpenOrchestra\DisplayBundle\BBcode\InternalLinkDefinition;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class NodeInNodeReferenceStrategy
 */
class NodeInNodeReferenceStrategy implements ReferenceStrategyInterface
{
    protected $nodeRepository;
    protected $bbcodeParser;
    protected $currentSiteManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param BBcodeParserInterface   $bbcodeParser
     * @param CurrentSiteIdInterface  $currentSiteManager
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        BBcodeParserInterface $bbcodeParser,
        CurrentSiteIdInterface $currentSiteManager
    ) {
        $this->nodeRepository = $nodeRepository;
        $this->bbcodeParser = $bbcodeParser;
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @param mixed $entity
     *
     * @return boolean
     */
    public function support($entity)
    {
        return ($entity instanceof ReadNodeInterface);
    }

    /**
     * @param mixed $entity
     */
    public function addReferencesToEntity($entity)
    {
        if ($this->support($entity)) {
            $listNodes = $this->extractNodesFromNode($entity);

            foreach ($listNodes as $nodeReference) {
                $nodeId = $nodeReference['nodeId'];
                $siteId = $nodeReference['siteId'];
                $this->nodeRepository->updateUseReference($entity->getId(), $nodeId, $siteId, NodeInterface::ENTITY_TYPE);
            }
        }
    }

    /**
     * @param mixed $entity
     */
    public function removeReferencesToEntity($entity)
    {
        if ($this->support($entity)) {
            $nodeId = $entity->getId();

            $nodeUsedInNode = $this->nodeRepository->findByUsedInEntity($nodeId, NodeInterface::ENTITY_TYPE);

            /** @var NodeInterface $node */
            foreach ($nodeUsedInNode as $node) {
                $node->removeUseInEntity($nodeId, NodeInterface::ENTITY_TYPE);
            }
        }
    }

    /**
     * @param ReadNodeInterface $node
     *
     * @return array
     */
    protected function extractNodesFromNode(ReadNodeInterface $node)
    {
        $references = array();

        /** @var \OpenOrchestra\ModelInterface\Model\BlockInterface $block */
        foreach ($node->getBlocks() as $block) {
            $references = array_merge($references, $this->extractNodesFromElement($block->getAttributes()));
        }

        return $references;
    }

    /**
     * Recursively extract content ids from elements (bloc, attribute, collection attribute, etc ...)
     *
     * @param mixed $element
     *
     * @return array
     */
    protected function extractNodesFromElement($element)
    {
        $references = array();
        if ($this->isNodeChoiceAttribute($element)) {
           $references[] = $this->extractNodeFromChoiceAttribute($element);
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
     * @param $str
     *
     * @return boolean
     */
    protected function hasInternalLinkBBCode($str)
    {
        $internalLinkBBCode = '/\[' . InternalLinkDefinition::TAG_NAME . '(\=\{.*\})?].*\[\/'
            . InternalLinkDefinition::TAG_NAME . '\]/m';

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
            $nodeId = $linkAttribute['site_nodeId'];
            $siteId = $linkAttribute['site_siteId'];

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
            $siteId = $this->currentSiteManager->getCurrentSiteId();
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
