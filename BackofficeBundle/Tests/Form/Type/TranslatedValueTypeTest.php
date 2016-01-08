<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\TranslatedValueType;

/**
 * Class TranslatedValueTypeTest
 */
class TranslatedValueTypeTest extends AbstractBaseTestCase
{
    /**
     * @var TranslatedValueType
     */
    protected $form;

    protected $builder;
    protected $translatedValueClass = 'translatedValueClass';
    protected $languages;
    
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->languages = array('en', 'fr');
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');

        $this->form = new TranslatedValueType($this->translatedValueClass, $this->languages);
    }

    /**
     * Test instance and name
     */
    public function testNameAndInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
        $this->assertSame('oo_translated_value', $this->form->getName());
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

        Phake::verify($this->builder)->addEventSubscriber(Phake::anyParameters());
    }
}
