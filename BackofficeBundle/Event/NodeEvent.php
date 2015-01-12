<?php

namespace PHPOrchestra\BackofficeBundle\Event;

use PHPOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class NodeEvent
 */
class NodeEvent extends Event
{
    protected $node;

    /**
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    /**
     * @return NodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }
}
