<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;

/**
 * class ContentStrategy
 */
class ContentStrategy extends AbstractBusinessRulesStrategy
{
    CONST DELETE_VERSION = 'DELETE_VERSION';

    protected $contentRepository;
    protected $siteRepository;
    protected $contextManager;

    /**
     * @param ContentRepositoryInterface $contentRepository
     * @param SiteRepositoryInterface    $siteRepository
     * @param CurrentSiteIdInterface     $contextManager
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository,
        SiteRepositoryInterface $siteRepository,
        CurrentSiteIdInterface   $contextManager
    ) {
        $this->contentRepository = $contentRepository;
        $this->siteRepository = $siteRepository;
        $this->contextManager = $contextManager;
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
            BusinessActionInterface::DELETE => 'canDelete',
            self::DELETE_VERSION => 'canDeleteVersion',
            BusinessActionInterface::EDIT => 'canEdit',
            BusinessActionInterface::READ => 'canRead',
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
        $siteId = $this->contextManager->getCurrentSiteId();
        $site = $this->siteRepository->findOneBySiteId($siteId);

        return in_array($content->getContentType(), $site->getContentTypes());
    }
}
