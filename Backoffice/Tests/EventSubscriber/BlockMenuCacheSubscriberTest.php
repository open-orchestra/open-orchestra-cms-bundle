<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\BlockMenuCacheSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ModelInterface\NodeEvents;

/**
 * Class BlockMenuCacheSubscriberTest
 */
class BlockMenuCacheSubscriberTest extends AbstractBaseTestCase
{
    protected $cacheableManager;
    protected $tagManager;
    protected $nodeEvent;
    protected $node;
    protected $nodeId = 'nodeId';
    protected $menuTag = 'menu-fake';
    protected $subscriber;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->cacheableManager = Phake::mock('OpenOrchestra\DisplayBundle\Manager\CacheableManager');
        $this->tagManager = Phake::mock('OpenOrchestra\BaseBundle\Manager\TagManager');
        Phake::when($this->tagManager)->formatMenuTag(Phake::anyParameters())->thenReturn($this->menuTag);

        $this->node = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($this->node)->getNodeId()->thenReturn($this->nodeId);

        $this->nodeEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\NodeEvent');
        Phake::when($this->nodeEvent)->getNode()->thenReturn($this->node);

        $this->subscriber = new BlockMenuCacheSubscriber($this->cacheableManager, $this->tagManager);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * @param string $eventName
     *
     * @dataProvider provideSubscribedEvent
     */
    public function testEventSubscribed($eventName)
    {
        $this->assertArrayHasKey($eventName, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(NodeEvents::PATH_UPDATED),
            array(NodeEvents::NODE_DELETE),
            array(NodeEvents::NODE_CHANGE_STATUS),
        );
    }

    /**
     * Test nodeChangeStatus
     */
    public function testInvalidateNodeTag()
    {
        $this->subscriber->invalidateNodeTag($this->nodeEvent);

        Phake::verify($this->cacheableManager)->invalidateTags(array($this->menuTag));
    }
}
