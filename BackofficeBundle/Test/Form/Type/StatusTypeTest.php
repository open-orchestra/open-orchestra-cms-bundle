<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\StatusType;
use Symfony\Component\Form\FormEvents;

/**
 * Class StatusTypeTest
 */
class StatusTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StatusType
     */
    protected $form;

    protected $statusClass = 'site';
    protected $translateValueInitializer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translateValueInitializer = Phake::mock('PHPOrchestra\BackofficeBundle\EventListener\TranslateValueInitializerListener');
        $this->form = new StatusType($this->statusClass, $this->translateValueInitializer);
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
        $this->assertSame('status', $this->form->getName());
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

        Phake::verify($builder)->add('name');
        Phake::verify($builder)->add('published', null, array('required' => false));
        Phake::verify($builder)->add('role', null, array('required' => false));
        Phake::verify($builder)->add('labels', 'translated_value_collection');
        Phake::verify($builder)->addEventSubscriber(Phake::anyParameters());
        Phake::verify($builder)->addEventListener(
            FormEvents::PRE_SET_DATA,
            array($this->translateValueInitializer, 'preSetData')
        );
    }

    /**
     * Test resolver
     */
    public function testSetDefaultOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'data_class' => $this->statusClass
        ));
    }
}
