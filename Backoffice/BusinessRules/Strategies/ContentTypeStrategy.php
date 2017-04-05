<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;

/**
 * class ContentTypeStrategy
 */
class ContentTypeStrategy extends AbstractBusinessRulesStrategy
{
    CONST READ_LIST = 'READ_LIST';

    protected $contentRepository;
    protected $contextManager;
    protected $siteRepository;

    /**
     * @param ContentRepositoryInterface $contentRepository
     * @param CurrentSiteIdInterface     $contextManager
     * @param SiteRepositoryInterface    $siteRepository
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository,
        CurrentSiteIdInterface     $contextManager,
        SiteRepositoryInterface    $siteRepository
    ) {
        $this->contentRepository = $contentRepository;
        $this->contextManager = $contextManager;
        $this->siteRepository = $siteRepository;
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
            ContentTypeStrategy::READ_LIST      => 'canReadList',
        );
    }

    /**
     * @param ContentTypeInterface $contentType
     * @param array                $parameters
     *
     * @return boolean
     */
    public function canDelete(ContentTypeInterface $contentType, array $parameters)
    {
        return 0 == $this->contentRepository->countByContentType($contentType->getContentTypeId());
    }

    /**
     * @param ContentTypeInterface $contentType
     * @param array                $parameters
     *
     * @return boolean
     */
    public function canReadList(ContentTypeInterface $contentType, array $parameters)
    {
        $siteId = $this->contextManager->getCurrentSiteId();
        $site = $this->siteRepository->findOneBySiteId($siteId);

        return in_array($contentType->getContentTypeId(), $site->getContentTypes());
    }
}
