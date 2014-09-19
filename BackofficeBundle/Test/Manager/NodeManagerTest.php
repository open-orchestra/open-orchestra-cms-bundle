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
        $this->nodeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');
    }

    
    /**
     * @param NodeInterface   $node
     * @param int   $expectedVersion
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
        $node = new Node();
        $node->setVersion(1);

        return array(
            array($node),
            array(2)
        );
    }
}
