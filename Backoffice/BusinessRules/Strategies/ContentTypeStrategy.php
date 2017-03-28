<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;

/**
 * class ContentTypeStrategy
 */
class ContentTypeStrategy extends AbstractBusinessRulesStrategy
{
    /**
     * @param ContentRepositoryInterface $contentRepository
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository
    ) {
        $this->contentRepository = $contentRepository;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return ContentTypeInterface::ENTITY_TYPE;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return array(
            ContributionActionInterface::DELETE => 'canDelete',
        );
    }

    /**
     * @param ContentTypeInterface $content
     * @param array            $parameters
     *
     * @return boolean
     */
    public function canDelete(ContentTypeInterface $contentType, array $parameters)
    {
        return 0 == $this->contentRepository->countByContentType($contentType->getContentTypeId());
    }
}
