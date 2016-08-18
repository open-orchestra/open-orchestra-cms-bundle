<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\BlockTransverseCacheSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;
use OpenOrchestra\ModelInterface\NodeEvents;

/**
 * Class BlockTransverseCacheSubscriberTest
 */
class BlockTransverseCacheSubscriberTest extends AbstractBaseTestCase
{
    protected $cacheableManager;
    protected $tagManager;
    protected $nodeEvent;
    protected $node;
    protected $nodeId = 'nodeId';
    protected $nodeTag = 'node-fake';
    protected $subscriber;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->cacheableManager = Phake::mock('OpenOrchestra\DisplayBundle\Manager\CacheableManager');
        $this->tagManager = Phake::mock('OpenOrchestra\BaseBundle\Manager\TagManager');
        Phake::when($this->tagManager)->formatNodeIdTag(Phake::anyParameters())->thenReturn($this->nodeTag);

        $this->node = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($this->node)->getNodeId()->thenReturn($this->nodeId);

        $this->nodeEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\NodeEvent');
        Phake::when($this->nodeEvent)->getNode()->thenReturn($this->node);

        $this->subscriber = new BlockTransverseCacheSubscriber($this->cacheableManager, $this->tagManager);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test event subscribed
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(NodeEvents::NODE_UPDATE_BLOCK, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param string $nodeType
     * @param int    $count
     *
     * @dataProvider provideNodeTypeAndCount
     */
    public function testInvalidateNodeWithBlockTransverseTag($nodeType, $count)
    {
        Phake::when($this->node)->getNodeType()->thenReturn($nodeType);
        $this->subscriber->invalidateNodeWithBlockTransverseTag($this->nodeEvent);

        Phake::verify($this->cacheableManager, Phake::times($count))->invalidateTags(array($this->nodeTag));
    }

    /**
     * @return array
     */
    public function provideNodeTypeAndCount()
    {
        return array(
            array(NodeInterface::TYPE_DEFAULT, 0),
            array(NodeInterface::TYPE_ERROR, 0),
            array(NodeInterface::TYPE_TRANSVERSE, 1),
        );
    }
}
