<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\TinymceType;

/**
 * Class TinymceTypeTest
 */
class TinymceTypeTest extends AbstractBaseTestCase
{
    /**
     * @var TinymceType
     */
    protected $form;

    protected $transformer;
    protected $builder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->transformer = Phake::mock('Symfony\Component\Form\DataTransformerInterface');
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        $this->form = new TinymceType($this->transformer);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('oo_tinymce', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder)->addModelTransformer($this->transformer);
    }

    /**
     * test parent
     */
    public function testParent()
    {
        $this->assertSame('textarea', $this->form->getParent());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'attr' => array(
                'class' => 'tinymce'
            )
        ));
    }
}
