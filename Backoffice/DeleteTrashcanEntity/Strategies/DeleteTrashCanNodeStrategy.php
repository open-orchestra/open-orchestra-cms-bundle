<?php

namespace OpenOrchestra\Backoffice\DeleteTrashcanEntity\Strategies;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\DeleteTrashcanEntity\DeleteTrashCanEntityInterface;
use OpenOrchestra\ModelInterface\Event\TrashcanEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\TrashItemRepositoryInterface;
use OpenOrchestra\ModelInterface\TrashcanEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class DeleteTrashCanNodeStrategy
 */
class DeleteTrashCanNodeStrategy implements DeleteTrashCanEntityInterface
{
    protected $nodeRepository;
    protected $trashItemRepository;
    protected $eventDispatcher;
    protected $objectManager;

    /**
     * @param NodeRepositoryInterface      $nodeRepository
     * @param TrashItemRepositoryInterface $trashItemRepository,
     * @param EventDispatcherInterface     $eventDispatcher
     * @param ObjectManager                $objectManager
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        TrashItemRepositoryInterface $trashItemRepository,
        EventDispatcherInterface $eventDispatcher,
        ObjectManager $objectManager
    )
    {
        $this->nodeRepository = $nodeRepository;
        $this->trashItemRepository = $trashItemRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->objectManager = $objectManager;
    }

    /**
     * @param mixed $entity
     *
     * @return bool
     */
    public function support($entity)
    {
        return $entity instanceof NodeInterface;
    }

    /**
     * @param mixed $entity
     */
    public function delete($entity)
    {
        $nodes = $this->nodeRepository->findByNodeAndSite($entity->getNodeId(), $entity->getSiteId());

        // remove children if node is the last parent
        if (count($nodes) == 1 ) {
            $subNodes = $this->nodeRepository->findByIncludedPathAndSiteId($entity->getPath(), $entity->getSiteId());
            $this->deletedNodes($subNodes);
        } else {
            $this->eventDispatcher->dispatch(TrashcanEvents::TRASHCAN_DELETE_ENTITY, new TrashcanEvent($entity));
            $this->objectManager->remove($entity);
        }

        $this->objectManager->flush();
    }

    /**
     * @param array $nodes
     */
    protected function deletedNodes(array $nodes)
    {
        foreach ($nodes as $node) {
            if ($node->isDeleted()) {
                $trashItem = $this->trashItemRepository->findByEntity($node->getId());
                if (null !== $trashItem) {
                    $this->objectManager->remove($trashItem);
                }
                $this->objectManager->remove($node);
                $this->eventDispatcher->dispatch(TrashcanEvents::TRASHCAN_DELETE_ENTITY, new TrashcanEvent($node));
            }
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'delete_trashcan_node';
    }
}
