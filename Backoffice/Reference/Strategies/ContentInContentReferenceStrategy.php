<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class ContentInContentReferenceStrategy
 */
class ContentInContentReferenceStrategy extends AbstractContentReferenceStrategy
{
    /**
     * @param mixed $entity
     *
     * @return boolean
     */
    public function support($entity)
    {
        return ($entity instanceof ContentInterface);
    }

    /**
     * @param mixed $entity
     */
    public function addReferencesToEntity($entity)
    {
        if ($this->support($entity)) {
            $element = $entity->getAttributes()->toArray();
            $contentIds = $this->extractContentsFromElement($element);

            foreach ($contentIds as $contentId) {
                /** @var \OpenOrchestra\ModelInterface\Model\ContentInterface $content */
                $contents = $this->contentRepository->findByContentId($contentId);
                foreach ($contents as $content) {
                    $content->addUseInEntity($entity->getId(), ContentInterface::ENTITY_TYPE);
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
            $contentId = $entity->getId();

            $contentsUsedInContent = $this->contentRepository
                ->findByUsedInEntity($contentId, ContentInterface::ENTITY_TYPE);

            foreach ($contentsUsedInContent as $content) {
                $content->removeUseInEntity($contentId, ContentInterface::ENTITY_TYPE);
            }
        }
    }
}
