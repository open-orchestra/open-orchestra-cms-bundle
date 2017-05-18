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
    protected $fieldTypeParameters;
    protected $fieldTypeClass = 'fieldTypeClass';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->fieldTypeParameters = array('text' => array(
            'label' => 'foo',
            'type' => 'text',
            'options' =>array(
                'max_length' => array('default_value' => 25)
            )
        ));

        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->builder)->add(Phake::anyParameters())->thenReturn($this->builder);

        $eventSubscriber = Phake::mock('Symfony\Component\EventDispatcher\EventSubscriberInterface');


        $this->resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');
        $contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface');
        Phake::when($contextManager)->getBackOfficeLanguage()->thenReturn('en');

        $this->form = new FieldTypeType(
            $contextManager,
            $eventSubscriber,
            array(),
            $this->fieldTypeParameters,
            $this->fieldTypeClass
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
                'attr' => array('class' => 'form-to-patch'),
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

        Phake::verify($this->builder, Phake::times(9))->add(Phake::anyParameters());
        Phake::verify($this->builder)->addEventSubscriber(Phake::anyParameters());
    }


    /**
     * test buildView
     */
    public function testBuildView()
    {
        $formInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        $formView = Phake::mock('Symfony\Component\Form\FormView');
        $fieldOption = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldOptionInterface');
        Phake::when($fieldOption)->getKey()->thenReturn('required');
        Phake::when($fieldOption)->getValue()->thenReturn(true);

        $formView->vars['columns']['labels']['data'] = array('en' => 'fakeEn');
        $formView->vars['columns']['type']['data'] =  'text';
        $formView->vars['columns']['options']['data'] = array($fieldOption);

        $this->form->buildView($formView, $formInterface, array());

        $this->assertEquals('fakeEn', $formView->vars['columns']['labels']['data']);
        $this->assertEquals('foo', $formView->vars['columns']['type']['data']);
        $this->assertEquals(array(
            'label' => 'open_orchestra_backoffice.form.orchestra_fields.required_field',
            'data' => 'open_orchestra_backoffice.form.swchoff.on',), $formView->vars['columns']['options']);
    }

    /**
     * Test form builder for prototype
     */
    public function testFormBuilderPrototype()
    {
        $closure = function() {return false;};

        $this->form->buildForm($this->builder, array('property_path' => null, 'prototype_data' => $closure));

        Phake::verify($this->builder, Phake::times(9))->add(Phake::anyParameters());
        Phake::verify($this->builder)->setData($closure());
        Phake::verify($this->builder)->addEventSubscriber(Phake::anyParameters());
    }
}
