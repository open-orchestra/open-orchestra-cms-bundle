<?php

namespace OpenOrchestra\GroupBundle\Tests\EventSubscriber;

use OpenOrchestra\GroupBundle\EventSubscriber\NodeGroupRoleForGroupSubscriber;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Phake;

/**
 * Class NodeGroupRoleForGroupSubscriberTest
 */
class NodeGroupRoleForGroupSubscriberTest extends AbstractNodeGroupRoleSubscriberTest
{
    /**
     * @var NodeGroupRoleForGroupSubscriber
     */
    protected $subscriber;
    protected $nodeRepository;
    protected $treeManager;
    protected $preUpdateEventArgs;
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

        $this->preUpdateEventArgs = Phake::mock('Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs');
        Phake::when($this->preUpdateEventArgs)->getDocumentManager()->thenReturn($this->documentManager);

        $this->subscriber = new NodeGroupRoleForGroupSubscriber($this->nodeGroupRoleClass, $this->treeManager);
        $this->subscriber->setContainer($this->container);
    }

    /**
     * test get subscribed events
     */
    public function testGetSubscribedEvents()
    {
        $this->assertSame($this->subscriber->getSubscribedEvents(),  array(
            'prePersist',
            'preUpdate',
        ));
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

        $this->subscriber->prePersist($this->lifecycleEventArgs);

        Phake::verify($this->group, Phake::times($countNodeGroupRole))->addModelGroupRole(Phake::anyParameters());
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
     * @param SiteInterface|null  $site
     * @param array               $nodes
     * @param bool                $hasChangeField
     * @param int                 $countNodeGroupRole
     *
     * @dataProvider provideGroupAndNodesUpdateSite
     */
    public function testPreUpdate($site, array $nodes, $hasChangeField, $countNodeGroupRole)
    {
        $countNodeGroupRole = count($this->nodesRoles) * $countNodeGroupRole;
        Phake::when($this->preUpdateEventArgs)->getDocument()->thenReturn($this->group);
        Phake::when($this->preUpdateEventArgs)->hasChangedField('site')->thenReturn($hasChangeField);
        Phake::when($this->group)->getSite()->thenReturn($site);
        Phake::when($this->treeManager)->generateTree(Phake::anyParameters())->thenReturn($nodes);

        $this->subscriber->preUpdate($this->preUpdateEventArgs);

        Phake::verify($this->group, Phake::times($countNodeGroupRole))->addModelGroupRole(Phake::anyParameters());
        $countRecompute = ($countNodeGroupRole > 0 ) ? 1 : 0;
        Phake::verify($this->uow, Phake::times($countRecompute))->recomputeSingleDocumentChangeSet(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideGroupAndNodesUpdateSite()
    {
        $nodeRoot = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $nodeChild = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');

        $treeNoChild = $this->createTree($nodeRoot);
        $treeChild = $this->createTree($nodeRoot, array($nodeChild));

        return array(
            array($site, $treeChild, true, 2),
            array($site, $treeChild, false, 0),
            array($site, $treeNoChild, true, 1),
            array(null, $treeNoChild, true, 0),
            array(null, array(), true, 0),
        );
    }

    /**
     * @param NodeInterface $root
     * @param array         $children
     *
     * @return array
     */
    protected function createTree($root, array $children = array())
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
