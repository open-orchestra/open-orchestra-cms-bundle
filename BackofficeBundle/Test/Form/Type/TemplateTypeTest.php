<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\TemplateType;

/**
 * Description of TemplateTypeTest
 */
class TemplateTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $formBuilder;
    protected $templateType;
    protected $nodeTypeTransformer;
    protected $templateClass = 'templateClass';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->formBuilder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->formBuilder)->addModelTransformer(Phake::anyParameters())->thenReturn($this->formBuilder);
        Phake::when($this->formBuilder)->add(Phake::anyParameters())->thenReturn($this->formBuilder);

        $this->templateType = new TemplateType($this->templateClass);
    }

    /**
     * test Build form method
     */
    public function testBuildForm()
    {
        $this->templateType->buildForm($this->formBuilder, array());

        Phake::verify($this->formBuilder, Phake::never())->addModelTransformer(Phake::anyParameters());
        Phake::verify($this->formBuilder, Phake::times(4))->add(Phake::anyParameters());
        Phake::verify($this->formBuilder, Phake::times(3))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * test set default option
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->templateType->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => $this->templateClass,
        ));
    }

    /**
     * test get name
     */
    public function testGetName()
    {
        $this->assertEquals('template', $this->templateType->getName());
    }
}
