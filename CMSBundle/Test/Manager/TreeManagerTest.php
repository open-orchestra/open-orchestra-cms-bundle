<?php

namespace PHPOrchestra\CMSBundle\Test\Manager;

use Phake;
use PHPOrchestra\CMSBundle\Manager\TreeManager;

/**
 * Class TreeManagerTest
 */
class TreeManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TreeManager
     */
    protected $manager;

    public function setUp()
    {
        $this->manager = new TreeManager();
    }

    /**
     * @param array $nodes
     * @param array $tree
     *
     * @dataProvider provideNodesAndTrees
     */
    public function testGenerateTree($nodes, $tree)
    {
//        shuffle($nodes);

        $generatedTree = $this->manager->generateTree($nodes);

        $this->assertSame($tree, $generatedTree);
    }

    /**
     * @return array
     */
    public function provideNodesAndTrees()
    {
        $rootNodeId = 'rootNodeId';
        $rootParentId = 'root';
        $childNodeId = 'childNodeId';
        $otherChildNodeId = 'otherChildNodeId';
        $grandChildNodeId = 'grandChildNodeId';
        $grandGrandChildNodeId = 'grandGrandChildNodeId';

        $superRootNode = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($superRootNode)->getNodeId()->thenReturn($rootParentId);
        Phake::when($superRootNode)->getParentId()->thenReturn('-');

        $rootNode = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($rootNode)->getNodeId()->thenReturn($rootNodeId);
        Phake::when($rootNode)->getParentId()->thenReturn($rootParentId);

        $childNode = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($childNode)->getNodeId()->thenReturn($childNodeId);
        Phake::when($childNode)->getParentId()->thenReturn($rootNodeId);

        $otherChildNode = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($otherChildNode)->getNodeId()->thenReturn($otherChildNodeId);
        Phake::when($otherChildNode)->getParentId()->thenReturn($rootNodeId);

        $grandChildNode = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($grandChildNode)->getNodeId()->thenReturn($grandChildNodeId);
        Phake::when($grandChildNode)->getParentId()->thenReturn($childNodeId);

        $grandGrandChildNode = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($grandGrandChildNode)->getNodeId()->thenReturn($grandGrandChildNodeId);
        Phake::when($grandGrandChildNode)->getParentId()->thenReturn($grandChildNodeId);

        return array(
            array(array(), array()),
            array(array($rootNode), array(array('node' => $rootNode, 'child' => array()))),
            array(array($rootNode, $rootNode), array(
                array('node' => $rootNode, 'child' => array()),
                array('node' => $rootNode, 'child' => array()),
            )),
            array(array($rootNode, $childNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $childNode, 'child' => array())
                ))
            )),
            array(array($rootNode, $childNode, $childNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $childNode, 'child' => array()),
                    array('node' => $childNode, 'child' => array())
                ))
            )),
            array(array($rootNode, $childNode, $grandChildNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $childNode, 'child' => array(
                        array('node' => $grandChildNode, 'child' => array())
                    )),
                ))
            )),
            array(array($grandChildNode, $rootNode, $childNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $childNode, 'child' => array(
                        array('node' => $grandChildNode, 'child' => array())
                    )),
                ))
            )),
            array(array($grandChildNode, $childNode, $rootNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $childNode, 'child' => array(
                        array('node' => $grandChildNode, 'child' => array())
                    )),
                ))
            )),
            array(array($childNode, $grandChildNode, $rootNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $childNode, 'child' => array(
                        array('node' => $grandChildNode, 'child' => array())
                    )),
                ))
            )),
            array(array($rootNode, $childNode, $grandChildNode, $grandGrandChildNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $childNode, 'child' => array(
                        array('node' => $grandChildNode, 'child' => array(
                            array('node' => $grandGrandChildNode, 'child' => array())
                        ))
                    )),
                ))
            )),
            array(array($grandGrandChildNode, $rootNode, $childNode, $grandChildNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $childNode, 'child' => array(
                        array('node' => $grandChildNode, 'child' => array(
                            array('node' => $grandGrandChildNode, 'child' => array())
                        ))
                    )),
                ))
            )),
            array(array($childNode, $grandGrandChildNode, $rootNode, $grandChildNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $childNode, 'child' => array(
                        array('node' => $grandChildNode, 'child' => array(
                            array('node' => $grandGrandChildNode, 'child' => array())
                        ))
                    )),
                ))
            )),
            array(array($grandChildNode, $childNode, $grandGrandChildNode, $rootNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $childNode, 'child' => array(
                        array('node' => $grandChildNode, 'child' => array(
                            array('node' => $grandGrandChildNode, 'child' => array())
                        ))
                    )),
                ))
            )),
            array(array($grandChildNode, $grandGrandChildNode, $childNode, $rootNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $childNode, 'child' => array(
                        array('node' => $grandChildNode, 'child' => array(
                            array('node' => $grandGrandChildNode, 'child' => array())
                        ))
                    )),
                ))
            )),
            array(array($grandChildNode, $grandGrandChildNode, $childNode, $rootNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $childNode, 'child' => array(
                        array('node' => $grandChildNode, 'child' => array(
                            array('node' => $grandGrandChildNode, 'child' => array())
                        ))
                    )),
                ))
            )),
            array(array($grandGrandChildNode, $rootNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $grandGrandChildNode, 'child' => array())
                )),
            )),
            array(array($grandGrandChildNode), array(
                array('node' => $grandGrandChildNode, 'child' => array())
            )),
            array(array($grandGrandChildNode, $superRootNode), array(
                array('node' => $superRootNode, 'child' => array(
                    array('node' => $grandGrandChildNode, 'child' => array())
                )),
            )),
            array(array($rootNode, $childNode, $grandChildNode, $grandChildNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $childNode, 'child' => array(
                        array('node' => $grandChildNode, 'child' => array()),
                        array('node' => $grandChildNode, 'child' => array()),
                    ))
                )),
            )),
            array(array($rootNode, $otherChildNode, $childNode, $grandChildNode, $grandChildNode), array(
                array('node' => $rootNode, 'child' => array(
                    array('node' => $otherChildNode, 'child' => array()),
                    array('node' => $childNode, 'child' => array(
                        array('node' => $grandChildNode, 'child' => array()),
                        array('node' => $grandChildNode, 'child' => array()),
                    )),
                )),
            )),
        );
    }
}
