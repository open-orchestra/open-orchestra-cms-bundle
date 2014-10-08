<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\NodeType;
use PHPOrchestra\ModelBundle\Model\TemplateInterface;

/**
 * Description of NodeTypeTest
 */
class NodeTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $nodeType;
    protected $nodeClass = 'nodeClass';
    protected $templateRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->templateRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\TemplateRepository');
        $this->nodeType = new NodeType($this->nodeClass, $this->templateRepository);
    }

    /**
     * test build form
     */
    public function testBuildForm()
    {
        $formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($formBuilderMock)->add(Phake::anyParameters())->thenReturn($formBuilderMock);



        $this->nodeType->buildForm($formBuilderMock, array());

        Phake::verify($formBuilderMock, Phake::times(6))->add(Phake::anyParameters());



        Phake::verify($formBuilderMock, Phake::never())->addModelTransformer(Phake::anyParameters());
        Phake::verify($formBuilderMock, Phake::times(3))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test the default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->nodeType->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => $this->nodeClass
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
