<?php

namespace OpenOrchestra\Backoffice\Manager;

use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Class NodePublisher
 */
class NodePublisher implements NodePublisherInterface
{
    protected $statusRepository;
    protected $nodeRepository;
    protected $objectManager;

    /**
     * @param StatusRepositoryInterface $statusRepository
     * @param NodeRepositoryInterface   $nodeRepository
     * @param ObjectManager             $objectManager
     */
    public function __construct(
        StatusRepositoryInterface $statusRepository,
        NodeRepositoryInterface $nodeRepository,
        ObjectManager $objectManager
    ) {
        $this->statusRepository = $statusRepository;
        $this->nodeRepository = $nodeRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @param ReadSiteInterface $site
     *
     * @return array | int   A published node list or an error code
     */
    public function publishNodes(ReadSiteInterface $site)
    {
        $fromStatus = $this->statusRepository->findByAutoPublishFrom();
        if (0 == count($fromStatus)) {

            return self::ERROR_NO_PUBLISH_FROM_STATUS;
        }

        $publishedStatus = $this->statusRepository->findOneByPublished();
        if (!$publishedStatus instanceof StatusInterface) {

            return self::ERROR_NO_PUBLISHED_STATUS;
        }

        $nodes = $this->nodeRepository->findNodeToAutoPublish($site->getSiteId(), $fromStatus);

        $publishedNodes = array();
        foreach ($nodes as $node) {
            $node->setStatus($publishedStatus);
            $this->objectManager->persist($node);
            $publishedNodes[] = array(
                'BOLabel' => $node->getBoLabel(),
                'version' => $node->getVersion(),
                'language' => $node->getLanguage()
            );
        }
        $this->objectManager->flush();

       return $publishedNodes;
    }

    /**
     * @param ReadSiteInterface $site
     *
     * @return array | int   An unpublished node list or an error code
     */
    public function unpublishNodes(ReadSiteInterface $site)
    {
        $publishedStatus = $this->statusRepository->findOneByPublished();
        if (!$publishedStatus instanceof StatusInterface) {

            return self::ERROR_NO_PUBLISHED_STATUS;
        }

        $unpublishedStatus = $this->statusRepository->findOneByAutoUnpublishTo();
        if (!$unpublishedStatus instanceof StatusInterface) {

            return self::ERROR_NO_UNPUBLISHED_STATUS;
        }

        $nodes = $this->nodeRepository->findNodeToAutoUnpublish($site->getSiteId(), $publishedStatus);

        $unpublishedNodes = array();
        foreach ($nodes as $node) {
            $node->setStatus($unpublishedStatus);
            $this->objectManager->persist($node);
            $unpublishedNodes[] = array(
                'BOLabel' => $node->getBoLabel(),
                'version' => $node->getVersion(),
                'language' => $node->getLanguage()
            );
        }
        $this->objectManager->flush();

        return $unpublishedNodes;
    }
}
