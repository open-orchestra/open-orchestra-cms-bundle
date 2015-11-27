<?php

namespace OpenOrchestra\GroupBundle\Tests\EventListener;

use OpenOrchestra\GroupBundle\EventListener\AddNodeGroupRoleForGroupListener;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Phake;

/**
 * Class AddNodeGroupRoleForGroupListenerTest
 */
class AddNodeGroupRoleForGroupListenerTest extends AbstractNodeGroupRoleListenerTest
{
    /**
     * @var AddNodeGroupRoleForGroupListener
     */
    protected $listener;
    protected $nodeRepository;

    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\NodeRepository');
        Phake::when($this->container)->get('open_orchestra_model.repository.node')->thenReturn($this->nodeRepository);
        $this->listener = new AddNodeGroupRoleForGroupListener($this->nodeGroupRoleClass);
        $this->listener->setContainer($this->container);
    }

    /**
     * test if the method is callable
     */
    public function testMethodPrePersistCallable()
    {
        $this->assertTrue(method_exists($this->listener, 'prePersist'));
    }

    /**
     * @param SiteInterface|null  $site
     * @param array               $nodes
     * @param int                 $countNodeGroupRole
     *
     * @dataProvider provideGroupAndNodes
     */
    public function testPrePersist($site, array $nodes, $countNodeGroupRole)
    {
        $group = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');

        $countNodeGroupRole = count($this->nodesRoles) * $countNodeGroupRole;
        Phake::when($this->lifecycleEventArgs)->getDocument()->thenReturn($group);
        Phake::when($group)->getSite()->thenReturn($site);
        Phake::when($this->nodeRepository)->findLastVersionByType(Phake::anyParameters())->thenReturn($nodes);

        $this->listener->prePersist($this->lifecycleEventArgs);

        Phake::verify($group, Phake::times($countNodeGroupRole))->addNodeRole(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideGroupAndNodes()
    {
        $node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $node2 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');

        return array(
            array($site, array($node1, $node2), 2),
            array($site, array($node1), 1),
            array(null, array($node1, $node2), 0),
            array(null, array(), 0),
        );
    }
}
