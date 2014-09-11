<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\FieldTypeType;
use Symfony\Component\Form\FormEvents;

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
    protected $translateValueInitializer;
    protected $translatedLabel = 'existing option';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->builder)->add(Phake::anyParameters())->thenReturn($this->builder);

        $this->resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->translatedLabel);

        $this->translateValueInitializer = Phake::mock('PHPOrchestra\BackofficeBundle\EventListener\TranslateValueInitializerListener');

        $this->form = new FieldTypeType($this->translator, $this->translateValueInitializer);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('field_type', $this->form->getName());
    }

    /**
     * Test resolver
     */
    public function testResolver()
    {
        $this->form->setDefaultOptions($this->resolver);

        Phake::verify($this->resolver)->setDefaults(array(
            'data_class' => 'PHPOrchestra\ModelBundle\Document\FieldType',
            'label' => $this->translatedLabel,
        ));
        Phake::verify($this->translator)->trans('php_orchestra_backoffice.form.field_type.label');
    }

    /**
     * Test form builder
     */
    public function testFormBuilder()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder, Phake::times(6))->add(Phake::anyParameters());
        Phake::verify($this->translator)->trans('php_orchestra_backoffice.form.field_option.add');
        Phake::verify($this->translator)->trans('php_orchestra_backoffice.form.field_option.new');
        Phake::verify($this->translator)->trans('php_orchestra_backoffice.form.field_option.delete');
        Phake::verify($this->builder)->addEventListener(
            FormEvents::PRE_SET_DATA,
            array($this->translateValueInitializer, 'preSetData')
        );
    }
}
