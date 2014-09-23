<?php

namespace PHPOrchestra\BackofficeBundle\Test\Manager;

use PHPOrchestra\BackofficeBundle\Manager\NodeManager;
use PHPOrchestra\ModelBundle\Document\Node;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use PHPOrchestra\ModelBundle\Repository\NodeRepository;
use Phake;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class NodeManagerTest
 */
class NodeManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeManager
     */
    protected $manager;

    /**
     * @var NodeRepositoryr
     */
    protected $nodeRepository;

    /**
     * @param NodeInterface   $node
     * @param int             $expectedVersion
     *
     * @dataProvider provideNode
     */
    public function testDuplicateNode(NodeInterface $node, $expectedVersion)
    {
        $nodeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');
        $manager = new NodeManager($nodeRepository);
        $alteredNode = $manager->duplicateNode($node);
        Phake::verify($alteredNode, Phake::times(1))->setVersion($expectedVersion);
    }

    /**
     * @param NodeRepository $nodeRepository
     * @param NodeInterface  $nodeToDelete
     * @param array          $nodes
     * 
     * @dataProvider provideNodeToDelete
     */
    public function testDeleteTree(NodeRepository $nodeRepository, NodeInterface $nodeToDelete, $nodes)
    {
        $manager = new NodeManager($nodeRepository);
        $manager->deleteTree($nodeToDelete);

        foreach($nodes as $node){
            Phake::verify($node, Phake::times(1))->setDeleted(true);
        }
    }

    /**
     * @return array
     */
    public function provideNode()
    {

        $node0 = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($node0)->getVersion()->thenReturn(0);

        $node1 = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($node1)->getVersion()->thenReturn(1);

        $node2 = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($node2)->getVersion()->thenReturn(null);

        return array(
            array($node0, 1),
            array($node1, 2),
            array($node2, 1),
        );
    }
    /**
     * @return array
     */
    public function provideNodeToDelete()
    {

        $nodeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');

        $nodesId = array('rootNodeId', 'childNodeId', 'otherChildNodeId');

        $nodes = array();
        foreach($nodesId as $nodeId){
            $nodes[$nodeId] = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
            Phake::when($nodes[$nodeId])->getNodeId()->thenReturn($nodeId);
        }

        $sons = new ArrayCollection();
        $sons->add($nodes[$nodesId[1]]);
        $sons->add($nodes[$nodesId[2]]);

        Phake::when($nodeRepository)->findByParentId($nodesId[0])->thenReturn($sons);
        Phake::when($nodeRepository)->findByParentId($nodesId[1])->thenReturn(new ArrayCollection());
        Phake::when($nodeRepository)->findByParentId($nodesId[2])->thenReturn(new ArrayCollection());

        return array(
            array($nodeRepository, $nodes[$nodesId[0]], $nodes)
        );
        
    }
}
