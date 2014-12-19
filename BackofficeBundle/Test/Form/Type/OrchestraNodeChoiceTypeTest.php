<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\OrchestraNodeChoiceType;

/**
 * Class OrchestraNodeChoiceType
 */
class OrchestraNodeChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $orchestraNodeChoiceType;
    protected $nodeRepository;
    protected $node1;
    protected $node2;
    protected $nodeName1 = 'node1';
    protected $nodeName2 = 'node2';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->node1 = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node1)->getName()->thenReturn($this->nodeName1);
        $this->node2 = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node2)->getName()->thenReturn($this->nodeName2);
        $this->nodeRepository = Phake::mock('PHPOrchestra\ModelInterface\Repository\NodeRepositoryInterface');

        $this->orchestraNodeChoiceType = new OrchestraNodeChoiceType($this->nodeRepository);
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

        $this->orchestraNodeChoiceType->setDefaultOptions($resolver);

        Phake::verify($resolver)->setDefaults(
            array(
                'choices' => array(
                    $this->nodeName1,
                    $this->nodeName2,
                )
            )
        );
    }
}
