<?php

namespace OpenOrchestra\GroupBundle\Tests\EventListener;

use OpenOrchestra\GroupBundle\EventListener\AddNodeGroupRoleForGroupListener;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
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
    protected $treeManager;
    protected $group;

    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\NodeRepository');
        $this->treeManager = Phake::mock('OpenOrchestra\DisplayBundle\Manager\TreeManager');
        Phake::when($this->container)->get('open_orchestra_model.repository.node')->thenReturn($this->nodeRepository);

        $this->group = $this->createMockGroup();
        $this->listener = new AddNodeGroupRoleForGroupListener($this->nodeGroupRoleClass, $this->treeManager);
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
        $countNodeGroupRole = count($this->nodesRoles) * $countNodeGroupRole;
        Phake::when($this->lifecycleEventArgs)->getDocument()->thenReturn($this->group);
        Phake::when($this->group)->getSite()->thenReturn($site);
        Phake::when($this->treeManager)->generateTree(Phake::anyParameters())->thenReturn($nodes);

        $this->listener->prePersist($this->lifecycleEventArgs);

        Phake::verify($this->group, Phake::times($countNodeGroupRole))->addNodeRole(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideGroupAndNodes()
    {
        $nodeRoot = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $nodeChild = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');

        $treeNoChild = $this->createTree($nodeRoot);
        $treeChild = $this->createTree($nodeRoot, array($nodeChild));

        return array(
            array($site, $treeChild, 2),
            array($site, $treeNoChild, 1),
            array(null, $treeNoChild, 0),
            array(null, array(), 0),
        );
    }

    /**
     * @param NodeInterface $root
     * @param array         $children
     *
     * @return array
     */
    public function createTree($root, array $children = array())
    {
        phake::when($root)->getNodeId()->thenReturn(NodeInterface::ROOT_NODE_ID);
        $childTree = array();
        foreach ($children as $child) {
            phake::when($child)->getParenId()->thenReturn(NodeInterface::ROOT_NODE_ID);
            $childTree[] = array(
                'node' => $child,
                'child' => array(),
            );
        }

        $tree = array();
        $tree[] = array(
            'node' => $root,
            'child' => $childTree
        );

        return $tree;
    }
}
