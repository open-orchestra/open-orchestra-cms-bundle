<?php

namespace OpenOrchestra\GroupBundle\Tests\EventSubscriber;

use OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface;
use OpenOrchestra\GroupBundle\EventSubscriber\UpdateNodeGroupRoleMoveNodeSubscriber;
use OpenOrchestra\ModelInterface\NodeEvents;
use Phake;

/**
 * Class UpdateNodeGroupRoleMoveNodeSubscriberTest
 */
class UpdateNodeGroupRoleMoveNodeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    protected $groupRepository;
    protected $nodeRepository;
    protected $roleCollector;
    protected $objectManager;
    protected $role = "ACCESS_ROLE";
    protected $nodeEvent;
    protected $fakeSiteId = "2";
    protected $fakeNodeId = "nodeId";
    protected $fakeParentId = "parentId";
    protected $group;

    /**
     * @var UpdateNodeGroupRoleMoveNodeSubscriber
     */
    protected $subscriber;

    /**
     * Set up the test
     */
    public function setUp()
    {

        $this->groupRepository = Phake::mock('OpenOrchestra\BackofficeBundle\Repository\GroupRepositoryInterface');
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->roleCollector = Phake::mock('OpenOrchestra\Backoffice\Collector\BackofficeRoleCollector');
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');

        $this->subscriber = new UpdateNodeGroupRoleMoveNodeSubscriber(
            $this->groupRepository,
            $this->nodeRepository,
            $this->roleCollector,
            $this->objectManager
        );

        $roles = array($this->role => 'fake_translation');
        Phake::when($this->roleCollector)->getRolesByType(Phake::anyParameters())->thenReturn($roles);

        $this->nodeEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\NodeEvent');
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getNodeId()->thenReturn($this->fakeNodeId);
        Phake::when($node)->getParentId()->thenReturn($this->fakeParentId);
        Phake::when($node)->getSiteId()->thenReturn($this->fakeSiteId);

        Phake::when($this->nodeEvent)->getNode()->thenReturn($node);

        $this->group = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getSiteId()->thenReturn($this->fakeSiteId);
        Phake::when($this->group)->getSite()->thenReturn($site);
    }

    /**
     * @param string  $accessType
     * @param bool    $accessNode
     * @param bool    $accessParent
     * @param integer $countUpdate
     *
     * @dataProvider provideAccessNodeNoChild
     */
    public function testUpdateAccessNodeGroupRoleWithNoChild($accessType, $accessNode, $accessParent, $countUpdate)
    {
        $nodeGroupRole = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');
        Phake::when($nodeGroupRole)->getAccessType()->thenReturn($accessType);
        Phake::when($nodeGroupRole)->isGranted()->thenReturn($accessNode);
        Phake::when($this->group)->getNodeRoleByNodeAndRole($this->fakeNodeId, $this->role)->thenReturn($nodeGroupRole);

        $nodeGroupRoleParent = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');
        Phake::when($nodeGroupRoleParent)->isGranted()->thenReturn($accessParent);
        Phake::when($this->group)->getNodeRoleByNodeAndRole($this->fakeParentId, $this->role)->thenReturn($nodeGroupRoleParent);

        $groups = array($this->group);

        Phake::when($this->groupRepository)->findAllWithSite()->thenReturn($groups);
        Phake::when($this->nodeRepository)->findByParent($this->fakeNodeId, $this->fakeSiteId)->thenReturn(array());

        $this->subscriber->updateAccessNodeGroupRole($this->nodeEvent);

        Phake::verify($nodeGroupRole, Phake::times($countUpdate))->setGranted($accessParent);
        Phake::verify($this->objectManager, Phake::times($countUpdate))->persist($this->group);
        Phake::verify($this->objectManager, Phake::times($countUpdate))->flush();
    }

    /**
     * Test Update Access NodeGroupRole With Exception
     */
    public function testUpdateAccessNodeGroupRoleWithException()
    {
        $nodeGroupRole = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');
        Phake::when($nodeGroupRole)->getAccessType()->thenReturn(NodeGroupRoleInterface::ACCESS_INHERIT);
        Phake::when($this->group)->getNodeRoleByNodeAndRole($this->fakeNodeId, $this->role)->thenReturn($nodeGroupRole);

        Phake::when($this->group)->getNodeRoleByNodeAndRole($this->fakeParentId, $this->role)->thenReturn(null);

        $groups = array($this->group);

        Phake::when($this->groupRepository)->findAllWithSite()->thenReturn($groups);
        Phake::when($this->nodeRepository)->findByParent($this->fakeNodeId, $this->fakeSiteId)->thenReturn(array());
        $this->setExpectedException('OpenOrchestra\GroupBundle\Exception\NodeGroupRoleNotFoundException');
        $this->subscriber->updateAccessNodeGroupRole($this->nodeEvent);
    }

    /**
     * @return array
     */
    public function provideAccessNodeNoChild()
    {
        return array(
            array(NodeGroupRoleInterface::ACCESS_INHERIT, true, true, 0),
            array(NodeGroupRoleInterface::ACCESS_DENIED, true, true, 0),
            array(NodeGroupRoleInterface::ACCESS_GRANTED, true, true, 0),
            array(NodeGroupRoleInterface::ACCESS_INHERIT, false, false, 0),
            array(NodeGroupRoleInterface::ACCESS_INHERIT, true, false, 1),
            array(NodeGroupRoleInterface::ACCESS_INHERIT, false, true, 1),
        );
    }

    /**
     * @param string  $accessType
     * @param bool    $accessNode
     * @param bool    $accessParent
     * @param string  $accessChildType
     * @param bool    $accessChild
     * @param integer $countUpdateNode
     * @param integer $countUpdateChild
     *
     * @dataProvider provideAccessNodeWithChild
     */
    public function testUpdateAccessNodeGroupRoleWithChild(
        $accessType,
        $accessNode,
        $accessParent,
        $accessChildType,
        $accessChild,
        $countUpdateNode,
        $countUpdateChild
    ) {
        $fakeChildId = "childId";

        $child = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($child)->getNodeId()->thenReturn($fakeChildId);
        Phake::when($child)->getParentId()->thenReturn($this->fakeNodeId);
        Phake::when($child)->getSiteId()->thenReturn($this->fakeSiteId);

        $nodeGroupRole = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');
        Phake::when($nodeGroupRole)->getAccessType()->thenReturn($accessType);
        Phake::when($nodeGroupRole)->isGranted()->thenReturn($accessNode);
        Phake::when($this->group)->getNodeRoleByNodeAndRole($this->fakeNodeId, $this->role)->thenReturn($nodeGroupRole);

        $nodeGroupChild = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');
        Phake::when($nodeGroupChild)->getAccessType()->thenReturn($accessChildType);
        Phake::when($nodeGroupChild)->getNodeId()->thenReturn($fakeChildId);
        Phake::when($nodeGroupChild)->isGranted()->thenReturn($accessChild);
        Phake::when($this->group)->getNodeRoleByNodeAndRole($fakeChildId, $this->role)->thenReturn($nodeGroupChild);

        $nodeGroupRoleParent = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');
        Phake::when($nodeGroupRoleParent)->isGranted()->thenReturn($accessParent);
        Phake::when($this->group)->getNodeRoleByNodeAndRole($this->fakeParentId, $this->role)->thenReturn($nodeGroupRoleParent);

        $groups = array($this->group);

        Phake::when($this->groupRepository)->findAllWithSite()->thenReturn($groups);
        Phake::when($this->nodeRepository)->findByParent($this->fakeNodeId, $this->fakeSiteId)->thenReturn(array($child));
        Phake::when($this->nodeRepository)->findByParent($fakeChildId, $this->fakeSiteId)->thenReturn(array());

        $this->subscriber->updateAccessNodeGroupRole($this->nodeEvent);

        $countTotal = $countUpdateNode + $countUpdateChild;
        Phake::verify($nodeGroupRole, Phake::times($countUpdateNode))->setGranted(Phake::anyParameters());
        Phake::verify($nodeGroupChild, Phake::times($countUpdateChild))->setGranted(Phake::anyParameters());
        Phake::verify($this->objectManager, Phake::times($countTotal))->persist(Phake::anyParameters());
        Phake::verify($this->objectManager, Phake::times($countTotal))->flush();
    }

    /**
     * @return array
     */
    public function provideAccessNodeWithChild()
    {
        $inherit = NodeGroupRoleInterface::ACCESS_INHERIT;
        $granted = NodeGroupRoleInterface::ACCESS_GRANTED;
        $denied = NodeGroupRoleInterface::ACCESS_DENIED;

        return array(
            array($inherit, true, true, $inherit, true, 0, 0),
            array($inherit, true, true, $inherit, false, 0, 1),
            array($inherit, true, true, $granted, false, 0, 0),
            array($inherit, true, true, $denied, false, 0, 0),
            array($denied, true, true, $inherit, true, 0, 0),
            array($denied, true, true, $inherit, false, 0, 1),
            array($granted, true, true, $inherit, true, 0, 0),
            array($granted, true, true, $inherit, false, 0, 1),
            array($inherit, true, false, $inherit, false, 1, 1),
            array($inherit, true, false, $inherit, true, 1, 0),
        );
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test subscribed events
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(NodeEvents::PATH_UPDATED, $this->subscriber->getSubscribedEvents());
    }
}
