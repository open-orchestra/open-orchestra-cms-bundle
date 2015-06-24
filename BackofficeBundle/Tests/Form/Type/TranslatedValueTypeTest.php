<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\TranslatedValueType;

/**
 * Class TranslatedValueTypeTest
 */
class TranslatedValueTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TranslatedValueType
     */
    protected $form;

    protected $builder;
    protected $translatedValueClass = 'translatedValueClass';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');

        $this->form = new TranslatedValueType($this->translatedValueClass);
    }

    /**
     * Test instance and name
     */
    public function testNameAndInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
        $this->assertSame('translated_value', $this->form->getName());
    }

    /**
     * Option resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'data_class' => $this->translatedValueClass
        ));
    }

    /**
     * Test buildForm
     */
    public function testBuildForm()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder)->add(Phake::anyParameters());
        Phake::verify($this->builder)->addEventSubscriber(Phake::anyParameters());
    }
}
