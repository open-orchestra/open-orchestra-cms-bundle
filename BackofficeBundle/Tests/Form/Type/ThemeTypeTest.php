<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\ThemeType;

/**
 * Class ThemeTypeTest
 */
class ThemeTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ThemeType
     */
    protected $form;

    protected $themeClass = 'theme';
    protected $translateValueInitializer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new ThemeType($this->themeClass);
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
        $this->assertSame('theme', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder)->add('name', null, array(
            'label' => 'open_orchestra_backoffice.form.theme.name'
        ));
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'data_class' => $this->themeClass
        ));
    }
}
