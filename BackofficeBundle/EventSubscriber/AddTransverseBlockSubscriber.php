<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\BlockNodeEvents;
use OpenOrchestra\ModelInterface\Event\BlockNodeEvent;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AddTransverseBlockSubscriber
 */
class AddTransverseBlockSubscriber implements EventSubscriberInterface
{
    protected $nodeRepository;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(NodeRepositoryInterface $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param BlockNodeEvent $event
     */
    public function addTransverseBlock(BlockNodeEvent $event)
    {
        $node = $event->getNode();
        $block = $event->getBlock();
        if ($node->getNodeType() != NodeInterface::TYPE_TRANSVERSE) {
            return;
        }

        $nodes = $this->nodeRepository->findByNodeTypeAndSite(NodeInterface::TYPE_TRANSVERSE, $node->getSiteId());
        /** @var NodeInterface $transverseNode */
        foreach ($nodes as $transverseNode) {
            if ($transverseNode->getId() == $node->getId()) {
                continue;
            }
            $newBlock = clone $block;
            $transverseNode->addBlock($newBlock);
            $blockIndex = $transverseNode->getBlockIndex($newBlock);
            /** @var AreaInterface $area */
            $area = $transverseNode->getAreas()->first();
            $area->addBlock(array('nodeId' => 0, 'blockId' => $blockIndex));
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            BlockNodeEvents::ADD_BLOCK_TO_NODE => 'addTransverseBlock',
        );
    }
}
