<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\FieldTypeType;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\ModelBundle\Document\FieldType;

/**
 * Class FieldTypeTypeTest
 */
class FieldTypeTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FieldTypeType
     */
    protected $form;

    protected $builder;
    protected $resolver;
    protected $translator;
    protected $fieldOptions;
    protected $translateValueInitializer;
    protected $translatedLabel = 'existing option';
    protected $fieldOptionClass = 'fieldOptionClass';
    protected $fieldTypeClass = 'fieldTypeClass';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->fieldOptions = array('text' => array(
            'label' => 'foo',
            'type' => 'text',
            'options' =>array(
                'max_length' => array('default_value' => 25)
            )
        ));

        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->builder)->add(Phake::anyParameters())->thenReturn($this->builder);

        $this->resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->translatedLabel);

        $this->translateValueInitializer = Phake::mock('OpenOrchestra\BackofficeBundle\EventListener\TranslateValueInitializerListener');

        $this->form = new FieldTypeType($this->translator, $this->translateValueInitializer, $this->fieldOptions, $this->fieldOptionClass, $this->fieldTypeClass);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('oo_field_type', $this->form->getName());
    }

    /**
     * Test resolver
     */
    public function testResolver()
    {
        $this->form->configureOptions($this->resolver);

        Phake::verify($this->resolver)->setDefaults(array(
            'data_class' => $this->fieldTypeClass,
            'label' => $this->translatedLabel,
            'prototype_data' => function(){
                $default = each($this->fieldOptions);
                $fieldType = new FieldType();
                $fieldType->setType($default['key']);

                return $fieldType;
            }
        ));
        Phake::verify($this->translator)->trans('open_orchestra_backoffice.form.field_type.label');
    }

    /**
     * Test form builder
     */
    public function testFormBuilder()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder, Phake::times(6))->add(Phake::anyParameters());
        Phake::verify($this->builder)->addEventListener(
            FormEvents::PRE_SET_DATA,
            array($this->translateValueInitializer, 'preSetData')
        );
        Phake::verify($this->builder)->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test form builder for prototype
     */
    public function testFormBuilderPrototype()
    {
        $closure = function() {return false;};

        $this->form->buildForm($this->builder, array('property_path' => null, 'prototype_data' => $closure));

        Phake::verify($this->builder, Phake::times(6))->add(Phake::anyParameters());
        Phake::verify($this->builder)->addEventListener(
            FormEvents::PRE_SET_DATA,
            array($this->translateValueInitializer, 'preSetData')
        );
        Phake::verify($this->builder)->setData($closure());
        Phake::verify($this->builder)->addEventSubscriber(Phake::anyParameters());
    }
}
