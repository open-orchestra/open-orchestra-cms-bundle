<?php

namespace PHPOrchestra\CMSBundle\Test\Form\Type;

use Phake;
use \PHPOrchestra\CMSBundle\Form\Type\NodeType;

/**
 * Description of NodeTypeTest
 */
class NodeTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $nodeType;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $router = Phake::mock('Symfony\Component\Routing\Router');
        Phake::when($router)->generate(Phake::anyParameters())->thenReturn('/dummy/url');

        $nodeTypeTransformer = Phake::mock('PHPOrchestra\CMSBundle\Form\DataTransformer\NodeTypeTransformer');

        $this->nodeType = new NodeType($nodeTypeTransformer, $router);
    }

    /**
     * test the build form
     */
    public function testBuildForm()
    {
        $formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($formBuilderMock)->add(Phake::anyParameters())->thenReturn($formBuilderMock);
        Phake::when($formBuilderMock)->addModelTransformer(Phake::anyParameters())->thenReturn($formBuilderMock);

        $this->nodeType->buildForm($formBuilderMock, array());

        Phake::verify($formBuilderMock, Phake::times(15))->add(Phake::anyParameters());
        Phake::verify($formBuilderMock)->addModelTransformer(Phake::anyParameters());
    }

    /**
     * Test the default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->nodeType->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'inDialog' => false,
            'beginJs' => array(),
            'endJs' => array(),
            'data_class' => 'Model\PHPOrchestraCMSBundle\Node',

        ));
    }

    /**
     * Test the form name
     */
    public function testGetName()
    {
        $this->assertEquals('node', $this->nodeType->getName());
    }
}
