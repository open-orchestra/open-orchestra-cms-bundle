<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\UpdateRedirectionNodeSubscriber;
use OpenOrchestra\ModelInterface\NodeEvents;

/**
 * Test UpdateRedirectionNodeSubscriberTest
 */
class UpdateRedirectionNodeSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var UpdateRedirectionNodeSubscriber
     */
    protected $subscriber;

    protected $node;
    protected $status;
    protected $nodeEvent;
    protected $nodeRepository;
    protected $language = 'fr';
    protected $nodeId = 'nodeId';
    protected $otherNodeId = 'other_nodeId';
    protected $siteId = 'fakeSiteId';
    protected $redirectionManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->status)->isPublished()->thenReturn(false);

        $this->previousStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node)->getStatus()->thenReturn($this->status);
        Phake::when($this->node)->getNodeId()->thenReturn($this->nodeId);
        Phake::when($this->node)->getSiteId()->thenReturn($this->siteId);
        Phake::when($this->node)->getLanguage()->thenReturn($this->language);

        $childNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($childNode)->getNodeId()->thenReturn($this->otherNodeId);
        Phake::when($childNode)->getSiteId()->thenReturn($this->siteId);

        $this->nodeEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\NodeEvent');
        Phake::when($this->nodeEvent)->getNode()->thenReturn($this->node);
        Phake::when($this->nodeEvent)->getPreviousStatus()->thenReturn($this->previousStatus);

        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        Phake::when($this->nodeRepository)->findByParent($this->nodeId, $this->siteId)->thenReturn(array($childNode, $childNode));
        Phake::when($this->nodeRepository)->findByParent($this->otherNodeId, $this->siteId)->thenReturn(array());

        $this->redirectionManager = Phake::mock('OpenOrchestra\Backoffice\Manager\RedirectionManager');

        $this->subscriber = new UpdateRedirectionNodeSubscriber($this->nodeRepository, $this->redirectionManager);

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
        $this->assertArrayHasKey(NodeEvents::NODE_DELETE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param boolean $isPublished
     * @param integer $nbrCall
     *
     * @dataProvider providePublished
     */
    public function testUpdateRedirection($isPublished, $nbrCall)
    {
        Phake::when($this->previousStatus)->isPublished()->thenReturn($isPublished);
        $this->subscriber->updateRedirection($this->nodeEvent);
        Phake::verify($this->redirectionManager, Phake::times($nbrCall))->generateRedirectionForNode($this->node);
    }

    /**
     * @return array
     */
    public function providePublished()
    {
        return array(
            array(true, 1),
            array(false, 0),
        );
    }

    /**
     * test updateRedirectionRoutes
     */
    public function testUpdateRedirectionRoutes()
    {
        $this->subscriber->updateRedirectionRoutes($this->nodeEvent);
        Phake::verify($this->redirectionManager)->updateRedirection($this->nodeId, $this->language, $this->siteId);
    }

    /**
     * test updateRedirectionRoutes
     */
    public function testUpdateRedirectionRoutesOnNodeDelete()
    {
        $this->subscriber->updateRedirectionRoutesOnNodeDelete($this->nodeEvent);
        Phake::verify($this->redirectionManager, Phake::times(3))->deleteRedirection(Phake::anyParameters());
    }
}
