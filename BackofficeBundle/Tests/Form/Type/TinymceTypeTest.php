<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\TinymceType;

/**
 * Class TinymceTypeTest
 */
class TinymceTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TinymceType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new TinymceType();
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
        $this->assertSame('tinymce', $this->form->getName());
    }

    /**
     * test parent
     */
    public function testParent()
    {
        $this->assertSame('textarea', $this->form->getParent());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'attr' => array(
                'class' => 'tinymce'
            )
        ));
    }
}
