<?php

namespace OpenOrchestra\GroupBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\GroupBundle\EventSubscriber\GroupPerimeterSubscriber;
use Phake;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\NodeEvents;

/**
 * Class GroupPerimeterSubscriberTest
 */
class GroupPerimeterSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var GroupPerimeterSubscriber
     */
    protected $subscriber;
    protected $groupRepository;
    protected $event;
    protected $nodeEvent;
    protected $nodePreviousPath = 'previousPath';
    protected $nodePath = 'nodePath';
    protected $siteId = 'siteId';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');

        $this->groupRepository = Phake::mock('OpenOrchestra\Backoffice\Repository\GroupRepositoryInterface');

        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getId()->thenReturn($this->siteId);
        $siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        Phake::when($siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($site);

        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getPath()->thenReturn($this->nodePath);

        $this->nodeEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\NodeEvent');
        Phake::when($this->nodeEvent)->getNode()->thenReturn($node);
        Phake::when($this->nodeEvent)->getPreviousPath()->thenReturn($this->nodePreviousPath);

        $this->subscriber = new GroupPerimeterSubscriber($this->groupRepository, $siteRepository);
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
        $this->assertArrayHasKey(FormEvents::POST_SUBMIT, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::PATH_UPDATED, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_HARD_DELETED, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test postSubmit
     */
    public function testPostSubmit()
    {
        $perimeter = Phake::mock('OpenOrchestra\Backoffice\Model\PerimeterInterface');
        Phake::when($perimeter)->getItems()->thenReturn(array());
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getSiteId()->thenReturn('fakeSited');
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group)->hasRole(Phake::anyParameters())->thenReturn(true);
        Phake::when($group)->getSite()->thenReturn($site);
        Phake::when($group)->getPerimeter(Phake::anyParameters())->thenReturn($perimeter);
        Phake::when($this->event)->getData()->thenReturn($group);

        $this->subscriber->postSubmit($this->event);

        Phake::verify($group)->addPerimeter(Phake::anyParameters());
    }

    public function testUpdateNodeInPerimeter()
    {
        $this->subscriber->updateNodeInPerimeter($this->nodeEvent);

        Phake::verify($this->groupRepository)->updatePerimeterItem(
            NodeInterface::ENTITY_TYPE,
            $this->nodePreviousPath,
            $this->nodePath,
            $this->siteId
        );
    }

    public function testRemoveNodeFromPerimeter()
    {
        $this->subscriber->removeNodeFromPerimeter($this->nodeEvent);

        Phake::verify($this->groupRepository)->removePerimeterItem(
            NodeInterface::ENTITY_TYPE,
            $this->nodePath,
            $this->siteId
        );
    }
}
