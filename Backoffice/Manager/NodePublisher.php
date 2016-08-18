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
    protected $fromStatus;
    protected $publishedStatus;
    protected $unpublishedStatus;
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
        $this->nodeRepository = $nodeRepository;
        $this->objectManager = $objectManager;

        $this->fromStatus = $statusRepository->findByAutoPublishFrom();
        $this->publishedStatus = $statusRepository->findOneByPublished();
        $this->unpublishedStatus = $statusRepository->findOneByAutoUnpublishTo();
    }

    /**
     * @param ReadSiteInterface $site
     *
     * @return array | int   A published node list or an error code
     */
    public function publishNodes(ReadSiteInterface $site)
    {
        if (0 == count($this->fromStatus)) {

            return self::ERROR_NO_PUBLISH_FROM_STATUS;
        }

        if (!$this->publishedStatus instanceof StatusInterface) {

            return self::ERROR_NO_PUBLISHED_STATUS;
        }

        $nodes = $this->nodeRepository->findNodeToAutoPublish($site->getSiteId(), $this->fromStatus);

        $publishedNodes = array();
        foreach ($nodes as $node) {
            $node->setStatus($this->publishedStatus);
            $this->objectManager->persist($node);
            $publishedNodes[] = array(
                'BOLabel' => $node->getBOLabel(),
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
        if (!$this->publishedStatus instanceof StatusInterface) {

            return self::ERROR_NO_PUBLISHED_STATUS;
        }

        if (!$this->unpublishedStatus instanceof StatusInterface) {

            return self::ERROR_NO_UNPUBLISHED_STATUS;
        }

        $nodes = $this->nodeRepository->findNodeToAutoUnpublish($site->getSiteId(), $this->publishedStatus);

        $unpublishedNodes = array();
        foreach ($nodes as $node) {
            $node->setStatus($this->unpublishedStatus);
            $this->objectManager->persist($node);
            $unpublishedNodes[] = array(
                'BOLabel' => $node->getBOLabel(),
                'version' => $node->getVersion(),
                'language' => $node->getLanguage()
            );
        }
        $this->objectManager->flush();

        return $unpublishedNodes;
    }
}
