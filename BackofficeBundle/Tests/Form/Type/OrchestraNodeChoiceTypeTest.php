<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\OrchestraNodeChoiceType;

/**
 * Class OrchestraNodeChoiceType
 */
class OrchestraNodeChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $orchestraNodeChoiceType;
    protected $nodeRepository;
    protected $treeManager;
    protected $node1;
    protected $node2;
    protected $nodeName1 = 'nodeName1';
    protected $nodeName2 = 'nodeName2';
    protected $nodeNodeId1 = 'nodeId1';
    protected $nodeNodeId2 = 'nodeId2';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node1)->getName()->thenReturn($this->nodeName1);
        Phake::when($this->node1)->getNodeId()->thenReturn($this->nodeNodeId1);
        $this->node2 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node2)->getName()->thenReturn($this->nodeName2);
        Phake::when($this->node2)->getNodeId()->thenReturn($this->nodeNodeId2);
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->treeManager = Phake::mock('OpenOrchestra\DisplayBundle\Manager\TreeManager');

        $this->orchestraNodeChoiceType = new OrchestraNodeChoiceType($this->nodeRepository, $this->treeManager);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->orchestraNodeChoiceType);
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertEquals('choice', $this->orchestraNodeChoiceType->getParent());
    }

    /**
     * Test Name
     */
    public function testGetName()
    {
        $this->assertEquals('orchestra_node_choice', $this->orchestraNodeChoiceType->getName());
    }

    /**
     * Test resolver
     */
    public function testSetDefaultOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        Phake::when($this->nodeRepository)->findLastVersionBySiteId()->thenReturn(
            array(
                $this->node1,
                $this->node2
            )
        );

        Phake::when($this->treeManager)->generateTree(Phake::anyParameters())->thenReturn(
            array(
                array('node' => $this->node1, 'child' => array(array('node' => $this->node2))),
            )
        );
        $this->orchestraNodeChoiceType->setDefaultOptions($resolver);
        Phake::verify($resolver)->setDefaults(
            array(
                'choices' => array(
                    $this->nodeNodeId1 => ''.$this->nodeName1,
                    $this->nodeNodeId2 => '&#x2514;'.$this->nodeName2,
                ),
                'attr' => array(
                    'class' => 'orchestra-node-choice'
                )
        ));
    }
}
