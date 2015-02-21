<?php

namespace OpenOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\MediaType;

/**
 * Class MediaTypeTest
 */
class MediaTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MediaType
     */
    protected $form;

    protected $mediaClass = 'site';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new MediaType($this->mediaClass);
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
        $this->assertSame('media', $this->form->getName());
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

        Phake::verify($builder)->add('file', 'file', array('label' => 'open_orchestra_backoffice.form.media.file'));
        Phake::verify($builder)->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test resolver
     */
    public function testSetDefaultOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'data_class' => $this->mediaClass
        ));
    }
}
