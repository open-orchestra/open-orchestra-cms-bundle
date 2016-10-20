<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;

/**
 * Class ContentInBlockReferenceStrategy
 */
class ContentInBlockReferenceStrategy implements ReferenceStrategyInterface
{
    protected $contentRepository;

    /**
     * @param ContentRepositoryInterface $contentRepository
     */
    public function __construct(ContentRepositoryInterface $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }

    /**
     * @param mixed $entity
     *
     * @return boolean
     */
    public function support($entity)
    {
        return ($entity instanceof ReadBlockInterface);
    }

    /**
     * @param mixed $entity
     */
    public function addReferencesToEntity($entity)
    {
        if ($this->support($entity)) {
            $element = $entity->getAttributes();
            if ($this->isContentSearchAttribute($element)) {
                $contents = $this->contentRepository->findByContentId($element['contentId']);
                if (is_array($contents)) {
                    foreach ($contents as $content) {
                        $content->addUseInEntity($entity->getId(), BlockInterface::ENTITY_TYPE);
                    }
                }
            }
        }
    }

    /**
     * @param mixed $entity
     */
    public function removeReferencesToEntity($entity)
    {
        if ($this->support($entity)) {
            $blockId = $entity->getId();

            $contentsUsedInBlock = $this->contentRepository->findByUsedInEntity($blockId, BlockInterface::ENTITY_TYPE);

            foreach ($contentsUsedInBlock as $content) {
                $content->removeUseInEntity($blockId, BlockInterface::ENTITY_TYPE);
            }
        }
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
}
