<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\OrchestraColorPickerType;

/**
 * Class OrchestraColorPickerTypeTest
 */
class OrchestraColorPickerTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraColorPickerType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new OrchestraColorPickerType();
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
        $this->assertEquals('orchestra_color_picker', $this->form->getName());
    }

    /**
     * Test resolver
     */
    public function testSetDefaultOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
                'attr' => array('class' => 'colorpicker')
        ));
    }
}
