<?php

namespace PHPOrchestra\BackofficeBundle\Test\Manager;

use PHPOrchestra\BackofficeBundle\Manager\NodeManager;
use PHPOrchestra\ModelBundle\Document\Node;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use Phake;

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
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');
        $this->manager = new NodeManager($this->nodeRepository);
    }

    /**
     * @param NodeInterface   $node
     * @param int             $expectedVersion
     *
     * @dataProvider provideNode
     */
    public function testDuplicateNode(NodeInterface $node, $expectedVersion)
    {
        $alteredNode = $this->manager->duplicateNode($node);
        $this->assertSame($alteredNode->getVersion(), $expectedVersion);
    }

    /**
     * @return array
     */
    public function provideNode()
    {
        $node0 = new Node();
        $node0->setVersion(0);

        $node1 = new Node();
        $node1->setVersion(1);

        $node2 = new Node();

        return array(
            array($node0, 1),
            array($node1, 2),
            array($node2, 1),
        );
    }
}
