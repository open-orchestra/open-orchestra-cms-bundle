<?php

namespace OpenOrchestra\Backoffice\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\RepositoryTrait\AutoPublishableTraitInterface;

/**
 * Class AutoPublishManager
 */
class AutoPublishManager implements AutoPublishManagerInterface
{
    protected $statusRepository;
    protected $repository;
    protected $objectManager;

    /**
     * @param StatusRepositoryInterface     $statusRepository
     * @param AutoPublishableTraitInterface $repository
     * @param ObjectManager                 $objectManager
     */
    public function __construct(
        StatusRepositoryInterface $statusRepository,
        AutoPublishableTraitInterface $repository,
        ObjectManager $objectManager
    ) {
        $this->statusRepository = $statusRepository;
        $this->repository = $repository;
        $this->objectManager = $objectManager;
    }

    /**
     * @param ReadSiteInterface $site
     *
     * @return array|int   A published elements list or an error code
     */
    public function publishElements(ReadSiteInterface $site)
    {
        $fromStatus = $this->statusRepository->findByAutoPublishFrom();
        if (0 == count($fromStatus)) {

            return self::ERROR_NO_PUBLISH_FROM_STATUS;
        }

        $publishedStatus = $this->statusRepository->findOneByPublished();
        if (!$publishedStatus instanceof StatusInterface) {

            return self::ERROR_NO_PUBLISHED_STATUS;
        }

        $elements = $this->repository->findElementToAutoPublish($site->getSiteId(), $fromStatus);

        $publishedElements = array();
        foreach ($elements as $element) {
            $element->setStatus($publishedStatus);
            $this->objectManager->persist($element);
            $publishedElements[] = array(
                'name' => $element->getName(),
                'version' => $element->getVersion(),
                'language' => $element->getLanguage()
            );
        }
        $this->objectManager->flush();

        return $publishedElements;
    }

    /**
     * @param ReadSiteInterface $site
     *
     * @return array|int   An unpublished element list or an error code
     */
    public function unpublishElements(ReadSiteInterface $site)
    {
        $publishedStatus = $this->statusRepository->findOneByPublished();
        if (!$publishedStatus instanceof StatusInterface) {

            return self::ERROR_NO_PUBLISHED_STATUS;
        }

        $unpublishedStatus = $this->statusRepository->findOneByAutoUnpublishTo();
        if (!$unpublishedStatus instanceof StatusInterface) {

            return self::ERROR_NO_UNPUBLISHED_STATUS;
        }

        $elements = $this->repository->findElementToAutoUnpublish($site->getSiteId(), $publishedStatus);

        $unpublishedElements = array();
        foreach ($elements as $element) {
            $element->setStatus($unpublishedStatus);
            $this->objectManager->persist($element);
            $unpublishedElements[] = array(
                'name' => $element->getName(),
                'version' => $element->getVersion(),
                'language' => $element->getLanguage()
            );
        }
        $this->objectManager->flush();

        return $unpublishedElements;
    }
}
