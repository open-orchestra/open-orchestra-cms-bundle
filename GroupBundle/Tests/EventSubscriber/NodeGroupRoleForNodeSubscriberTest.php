<?php

namespace OpenOrchestra\GroupBundle\Tests\EventSubscriber;

use OpenOrchestra\GroupBundle\EventSubscriber\NodeGroupRoleForNodeSubscriber;
use Phake;

/**
 * Class NodeGroupRoleForNodeSubscriberTest
 */
class NodeGroupRoleForNodeSubscriberTest extends AbstractNodeGroupRoleListenerTest
{
    /**
     * @var NodeGroupRoleForNodeSubscriber
     */
    protected $subscriber;
    protected $groupRepository;

    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->groupRepository = Phake::mock('OpenOrchestra\Backoffice\Repository\GroupRepositoryInterface');
        Phake::when($this->container)->get('open_orchestra_user.repository.group')->thenReturn($this->groupRepository);

        $this->subscriber = new NodeGroupRoleForNodeSubscriber($this->nodeGroupRoleClass);
        $this->subscriber->setContainer($this->container);
    }

    /**
     * test get subscribed events
     */
    public function testGetSubscribedEvents()
    {
        $this->assertSame($this->subscriber->getSubscribedEvents(),  array(
            'postPersist',
        ));
    }

    /**
     * @param array               $groups
     * @param int                 $countNodeGroupRole
     *
     * @dataProvider provideGroup
     */
    public function testPostPersist(array $groups, $countNodeGroupRole)
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $countNodeGroupRole = count($this->nodesRoles) * $countNodeGroupRole;
        Phake::when($this->lifecycleEventArgs)->getDocument()->thenReturn($node);
        Phake::when($this->groupRepository)->findAllWithSite()->thenReturn($groups);

        $this->subscriber->postPersist($this->lifecycleEventArgs);

        Phake::verify($this->documentManager, Phake::times($countNodeGroupRole))->persist(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideGroup()
    {
        $group1 = $this->createMockGroup();
        $group2 = $this->createMockGroup();
        $group3 = $this->createMockGroup();
        Phake::when($group3)->hasModelGroupRoleByTypeAndIdAndRole(Phake::anyParameters())->thenReturn(true);

        return array(
           array(array($group1, $group2), 2),
           array(array($group1), 1),
           array(array($group3), 0),
           array(array(), 0),
        );
    }
}
