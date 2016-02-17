<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\FieldOptionType;

/**
 * Class FieldOptionTypeTest
 */
class FieldOptionTypeTest extends AbstractBaseTestCase
{
    /**
     * @var FieldOptionType
     */
    protected $form;

    protected $builder;
    protected $resolver;
    protected $fieldOptionClass = 'fieldOptionClass';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->builder)->add(Phake::anyParameters())->thenReturn($this->builder);

        $this->resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form = new FieldOptionType(array(), $this->fieldOptionClass);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('oo_field_option', $this->form->getName());
    }

    /**
     * Test resolver
     */
    public function testResolver()
    {
        $this->form->configureOptions($this->resolver);

        Phake::verify($this->resolver)->setDefaults(array(
            'data_class' => $this->fieldOptionClass,
            'label' => 'open_orchestra_backoffice.form.field_option.label',
        ));
    }

    /**
     * Test form builder
     */
    public function testFormBuilder()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder)->add(Phake::anyParameters());
        Phake::verify($this->builder)->addEventSubscriber(Phake::anyParameters());
    }
}
