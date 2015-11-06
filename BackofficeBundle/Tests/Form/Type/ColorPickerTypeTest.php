<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\ColorPickerType;

/**
 * Class ColorPickerTypeTest
 */
class OrchestraColorPickerTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ColorPickerType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new ColorPickerType();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertEquals('text', $this->form->getParent());
    }

    /**
     * Test Name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_color_picker', $this->form->getName());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
                'attr' => array('class' => 'colorpicker')
        ));
    }
}
