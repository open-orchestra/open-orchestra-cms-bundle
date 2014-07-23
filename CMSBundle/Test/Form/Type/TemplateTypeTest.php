<?php

namespace PHPOrchestra\CMSBundle\Test\Form\Type;

use Phake;
use \PHPOrchestra\CMSBundle\Form\Type\TemplateType;

/**
 * Description of TemplateTypeTest
 *
 * @author Nicolas BOUQUET <nicolas.bouquet@businessdecision.com>
 */
class TemplateTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $templateType;
    protected $nodeTypeTransformer;
    protected $formBuilder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeTypeTransformer = Phake::mock('PHPOrchestra\CMSBundle\Form\DataTransformer\NodeTypeTransformer');

        $this->formBuilder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->formBuilder)->addModelTransformer(Phake::anyParameters())->thenReturn($this->formBuilder);
        Phake::when($this->formBuilder)->add(Phake::anyParameters())->thenReturn($this->formBuilder);

        $this->templateType = new TemplateType($this->nodeTypeTransformer);
    }

    /**
     * test Build form method
     */
    public function testBuildForm()
    {
        $this->templateType->buildForm($this->formBuilder, array());

        Phake::verify($this->formBuilder)->addModelTransformer($this->nodeTypeTransformer);
        Phake::verify($this->formBuilder, Phake::times(11))->add(Phake::anyParameters());
    }

    /**
     * test set default option
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->templateType->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'inDialog' => false,
            'beginJs' => array(),
            'endJs' => array()
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
