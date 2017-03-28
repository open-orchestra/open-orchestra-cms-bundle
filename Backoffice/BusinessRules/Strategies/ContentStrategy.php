<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;

/**
 * class ContentStrategy
 */
class ContentStrategy extends AbstractBusinessRulesStrategy
{

    CONST DELETE_VERSION = 'DELETE_VERSION';

    /**
     * @param ContentRepositoryInterface $contentRepository
     * @param SiteRepositoryInterface    $siteRepository
     * @param ContextManager             $contextManeger
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository,
        SiteRepositoryInterface $siteRepository,
        ContextManager $contextManeger
    ) {
        $this->contentRepository = $contentRepository;
        $this->siteRepository = $siteRepository;
        $this->contextManeger = $contextManeger;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return ContentInterface::ENTITY_TYPE;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return array(
            ContributionActionInterface::DELETE => 'canDelete',
            self::DELETE_VERSION => 'canDeleteVersion',
            ContributionActionInterface::EDIT => 'canEdit',
            ContributionActionInterface::READ => 'canRead',
        );
    }

    /**
     * @param ContentInterface $content
     * @param array            $parameters
     *
     * @return boolean
     */
    public function canDelete(ContentInterface $content, array $parameters)
    {
        return $this->isContentOnSiteAllowed($content) && false === $this->contentRepository->hasContentIdWithoutAutoUnpublishToState($content->getContentId());
    }

    /**
     * @param ContentInterface $content
     * @param array            $parameters
     *
     * @return boolean
     */
    public function canDeleteVersion(ContentInterface $content, array $parameters)
    {
        return $this->contentRepository->countNotDeletedByLanguage($content->getContentId(), $content->getLanguage()) > 1 && $this->isContentOnSiteAllowed($content) && !$content->getStatus()->isPublishedState();
    }

    /**
     * @param ContentInterface $content
     * @param array            $parameters
     *
     * @return boolean
     */
    public function canEdit(ContentInterface $content, array $parameters)
    {
        return $this->isContentOnSiteAllowed($content, $parameters) && !$content->getStatus()->isBlockedEdition();
    }

    /**
     * @param ContentInterface $content
     * @param array            $parameters
     *
     * @return boolean
     */
    public function canRead(ContentInterface $content, array $parameters)
    {
        return $this->isContentOnSiteAllowed($content);
    }

    /**
     * @param ContentInterface $content
     *
     * @return bool
     */
    protected function isContentOnSiteAllowed(ContentInterface $content)
    {
        $siteId = $this->contextManeger->getCurrentSiteId();
        $site = $this->siteRepository->findOneBySiteId($siteId);

        return in_array($content->getContentType(), $site->getContentTypes());
    }
}
