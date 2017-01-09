<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\FieldTypeType;

/**
 * Class FieldTypeTypeTest
 */
class FieldTypeTypeTest extends AbstractBaseTestCase
{
    /**
     * @var FieldTypeType
     */
    protected $form;

    protected $builder;
    protected $resolver;
    protected $fieldOptions;
    protected $fieldTypeSearchable;
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

        $this->fieldTypeSearchable = array(
            "text" => array("search" => 'text'),
            "date" => array("search" => 'date'),
        );

        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->builder)->add(Phake::anyParameters())->thenReturn($this->builder);

        $this->resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');
        $contextManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');

        $this->form = new FieldTypeType(
            $contextManager,
            $this->fieldOptions,
            $this->fieldOptionClass,
            $this->fieldTypeClass,
            $this->fieldTypeSearchable,
            array()
        );
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

        Phake::verify($this->resolver)->setDefaults(
            array(
                'data_class' => $this->fieldTypeClass,
                'group_enabled' => true,
                'sub_group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.field_type.sub_group.property',
                    ),
                    'parameter' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.field_type.sub_group.parameter',
                    ),
                ),
                'columns' => array('labels', 'fieldId', 'type', 'options'),
                'label' => 'open_orchestra_backoffice.form.field_type.label',
                'prototype_data' => function(){
                    $default = each($this->fieldOptions);
                    $fieldType = new $this->fieldTypeClass();
                    $fieldType->setType($default['key']);

                    return $fieldType;
                }
            )
        );
    }

    /**
     * Test form builder
     */
    public function testFormBuilder()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder, Phake::times(7))->add(Phake::anyParameters());
        Phake::verify($this->builder)->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test form builder for prototype
     */
    public function testFormBuilderPrototype()
    {
        $closure = function() {return false;};

        $this->form->buildForm($this->builder, array('property_path' => null, 'prototype_data' => $closure));

        Phake::verify($this->builder, Phake::times(7))->add(Phake::anyParameters());
        Phake::verify($this->builder)->setData($closure());
        Phake::verify($this->builder)->addEventSubscriber(Phake::anyParameters());
    }
}
