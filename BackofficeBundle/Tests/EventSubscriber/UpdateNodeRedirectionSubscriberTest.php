<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\EventSubscriber\UpdateNodeRedirectionSubscriber;
use OpenOrchestra\ModelInterface\NodeEvents;

/**
 * Test UpdateNodeRedirectionSubscriberTest
 */
class UpdateNodeRedirectionSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var UpdateNodeRedirectionSubscriber
     */
    protected $subscriber;

    protected $node;
    protected $otherNode;
    protected $status;
    protected $id = 'id';
    protected $nodeEvent;
    protected $nodeRepository;
    protected $language = 'fr';
    protected $nodeId = 'nodeId';
    protected $siteId = 'fakeSiteId';
    protected $redirectionManager;
    protected $routePattern = 'route_pattern';
    protected $otherRoutePattern = 'other_route_pattern';
    protected $currentSiteManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->status)->isPublished()->thenReturn(false);
        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node)->getId()->thenReturn($this->id);
        Phake::when($this->node)->getStatus()->thenReturn($this->status);
        Phake::when($this->node)->getNodeId()->thenReturn($this->nodeId);
        Phake::when($this->node)->getParentId()->thenReturn($this->nodeId);
        Phake::when($this->node)->getLanguage()->thenReturn($this->language);
        Phake::when($this->node)->getRoutePattern()->thenReturn($this->routePattern);
        Phake::when($this->node)->getSiteId()->thenReturn($this->siteId);
        $this->otherNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->otherNode)->getId()->thenReturn($this->id);
        Phake::when($this->otherNode)->getNodeId()->thenReturn($this->nodeId);
        Phake::when($this->otherNode)->getParentId()->thenReturn(null);
        Phake::when($this->otherNode)->getLanguage()->thenReturn($this->language);
        Phake::when($this->otherNode)->getRoutePattern()->thenReturn($this->otherRoutePattern);
        $this->nodeEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\NodeEvent');
        Phake::when($this->nodeEvent)->getNode()->thenReturn($this->node);
        Phake::when($this->nodeEvent)->getPreviousStatus()->thenReturn($this->status);
        $this->siteAliasEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\SiteAliasEvent');
        Phake::when($this->siteAliasEvent)->getSiteId()->thenReturn($this->siteId);
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->redirectionManager = Phake::mock('OpenOrchestra\BackofficeBundle\Manager\RedirectionManager');
        $this->currentSiteManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');

        $this->subscriber = new UpdateNodeRedirectionSubscriber($this->nodeRepository, $this->redirectionManager, $this->currentSiteManager);
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
        $this->assertArrayHasKey(NodeEvents::NODE_CHANGE_STATUS, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_RESTORE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test with no previous node
     */
    public function testUpdateRedirectionWithNoPreviousNode()
    {
        Phake::when($this->nodeRepository)->findPublishedSortedByVersion(Phake::anyParameters())
            ->thenReturn(array($this->node));

        $this->subscriber->updateRedirection($this->nodeEvent);

        Phake::verify($this->redirectionManager, Phake::never())->createRedirection(Phake::anyParameters());
    }

    /**
     * Test with no previous node with same pattern
     */
    public function testUpdateRedirectionWithPreviousNodeAndSamePattern()
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getId()->thenReturn('other');
        Phake::when($node)->getRoutePattern()->thenReturn($this->routePattern);

        Phake::when($this->nodeRepository)->findPublishedSortedByVersion(Phake::anyParameters())
            ->thenReturn(array($this->node, $node));

        $this->subscriber->updateRedirection($this->nodeEvent);

        Phake::verify($this->redirectionManager, Phake::never())->createRedirection(Phake::anyParameters());
    }

    /**
     * Test with no previous node with different pattern
     */
    public function testUpdateRedirectionWithPreviousNodeAndDifferentPattern()
    {
        Phake::when($this->status)->isPublished()->thenReturn(true);
        $oldPattern = 'oldPattern';
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getId()->thenReturn('other');
        Phake::when($node)->getRoutePattern()->thenReturn($oldPattern);
        Phake::when($node)->getParentId()->thenReturn('fakeParentId');
        Phake::when($node)->getLanguage()->thenReturn('fakeLanguage');

        Phake::when($node)->getRoutePattern()->thenReturn($oldPattern);
        $parent = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($parent)->getRoutePattern()->thenReturn($oldPattern);

        Phake::when($this->nodeRepository)->findPublishedInLastVersion(Phake::anyParameters())
            ->thenReturn($parent);
        Phake::when($this->nodeRepository)->findPublishedSortedByVersion(Phake::anyParameters())
            ->thenReturn(array($this->node, $node));

        $this->subscriber->updateRedirection($this->nodeEvent);

        Phake::verify($this->redirectionManager)->createRedirection($oldPattern . '/' . $oldPattern, $this->nodeId, $this->language);
    }

    /**
     * Test update redirection routes
     */
    public function testUpdateRedirectionRoutes()
    {
        $this->subscriber->updateRedirectionRoutes($this->nodeEvent);

        Phake::verify($this->redirectionManager)->updateRedirection($this->nodeId, $this->language);
    }

    /**
     * Test update site alias
     */
    public function testUpdateRedirectionOnSiteAliasUpdate()
    {
        Phake::when($this->nodeRepository)->findLastVersionBySiteId(Phake::anyParameters())
        ->thenReturn(array($this->node));
        Phake::when($this->nodeRepository)->findPublishedSortedByVersion(Phake::anyParameters())
        ->thenReturn(array($this->node, $this->otherNode));

        $this->subscriber->updateRedirectionOnSiteAliasUpdate($this->siteAliasEvent);

        Phake::verify($this->redirectionManager)->deleteRedirection($this->nodeId, $this->language);
        Phake::verify($this->redirectionManager)->createRedirection(
            $this->otherRoutePattern,
            $this->nodeId,
            $this->language
        );
    }
}
