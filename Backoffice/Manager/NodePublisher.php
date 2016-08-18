<?php

namespace OpenOrchestra\Backoffice\Manager;

use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class NodePublisher
 */
class NodePublisher
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
     * @return array
     */
    public function publishNodes(ReadSiteInterface $site)
    {
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
     * @return array
     */
    public function unpublishNodes(ReadSiteInterface $site)
    {
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
