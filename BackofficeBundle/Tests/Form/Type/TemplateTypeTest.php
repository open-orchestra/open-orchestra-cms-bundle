<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\TemplateType;

/**
 * Description of TemplateTypeTest
 */
class TemplateTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $formBuilder;
    protected $templateType;
    protected $nodeTypeTransformer;
    protected $areaClass = 'areaClass';
    protected $templateClass = 'templateClass';
    protected $translator;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->formBuilder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->formBuilder)->addModelTransformer(Phake::anyParameters())->thenReturn($this->formBuilder);
        Phake::when($this->formBuilder)->add(Phake::anyParameters())->thenReturn($this->formBuilder);
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->templateType = new TemplateType($this->templateClass, $this->areaClass, $this->translator);
    }

    /**
     * test Build form method
     */
    public function testBuildForm()
    {
        $this->templateType->buildForm($this->formBuilder, array());

        Phake::verify($this->formBuilder, Phake::never())->addModelTransformer(Phake::anyParameters());
        Phake::verify($this->formBuilder, Phake::times(3))->add(Phake::anyParameters());
        Phake::verify($this->formBuilder, Phake::times(2))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * test configureOptions
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->templateType->configureOptions($resolverMock);

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
