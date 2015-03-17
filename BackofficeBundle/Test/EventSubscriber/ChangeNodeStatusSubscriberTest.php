<?php

namespace OpenOrchestra\BackofficeBundle\Test\EventSubscriber;

use Phake;
use OpenOrchestra\BackofficeBundle\EventSubscriber\ChangeNodeStatusSubscriber;
use OpenOrchestra\ModelInterface\NodeEvents;

/**
 * Class ChangeNodeStatusSubscriberTest
 */
class ChangeNodeStatusSubscriberTest extends \PHPUnit_Framework_TestCase
{
    protected $cacheableManager;
    protected $tagManager;
    protected $nodeEvent;
    protected $node;
    protected $nodeId = 'nodeId';
    protected $nodeIdTag = 'nodeIdTag';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->cacheableManager = Phake::mock('OpenOrchestra\DisplayBundle\Manager\CacheableManager');
        $this->tagManager = Phake::mock('OpenOrchestra\DisplayBundle\Manager\TagManager');
        Phake::when($this->tagManager)->formatNodeIdTag(Phake::anyParameters())->thenReturn($this->nodeIdTag);

        $this->node = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($this->node)->getNodeId()->thenReturn($this->nodeId);

        $this->nodeEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\NodeEvent');
        Phake::when($this->nodeEvent)->getNode()->thenReturn($this->node);

        $this->subscriber = new ChangeNodeStatusSubscriber($this->cacheableManager, $this->tagManager);
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
            array(NodeEvents::NODE_CHANGE_STATUS),
        );
    }

    /**
     * Test nodeChangeStatus
     */
    public function testNodeChangeStatus()
    {
        $this->subscriber->nodeChangeStatus($this->nodeEvent);

        Phake::verify($this->cacheableManager)->invalidateTags(array($this->nodeIdTag));
    }
}
