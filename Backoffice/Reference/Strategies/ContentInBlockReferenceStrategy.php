<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;

/**
 * Class ContentInBlockReferenceStrategy
 */
class ContentInBlockReferenceStrategy extends AbstractContentReferenceStrategy
{
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
            $contentIds = $this->extractContentsFromElement($element);
            foreach ($contentIds as $contentId) {
                /** @var \OpenOrchestra\ModelInterface\Model\ContentInterface $content */
                $contents = $this->contentRepository->findByContentId($contentId);

                foreach ($contents as $content) {
                    $content->addUseInEntity($entity->getId(), BlockInterface::ENTITY_TYPE);
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
}
