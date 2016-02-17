<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\Component\NodeChoiceType;

/**
 * Class NodeChoiceType
 */
class NodeChoiceTypeTest extends AbstractBaseTestCase
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
        $currentSiteManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');

        $this->orchestraNodeChoiceType = new NodeChoiceType($this->nodeRepository, $this->treeManager, $currentSiteManager);
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
        $this->assertEquals('oo_node_choice', $this->orchestraNodeChoiceType->getName());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        Phake::when($this->nodeRepository)->findLastVersionByType(Phake::anyParameters())->thenReturn(
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
        $this->orchestraNodeChoiceType->configureOptions($resolver);
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
