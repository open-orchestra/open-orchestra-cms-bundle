<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\UserType;

/**
 * Class UserTypeTest
 */
class UserTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserType
     */
    protected $form;

    protected $class = 'OpenOrchestra\UserBundle\Document\User';

    protected $builder;
    protected $resolver;
    protected $translator;
    protected $string = 'string';

    /**
     * Set up common test part
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->string);

        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->builder)->add(Phake::anyParameters())->thenReturn($this->builder);
        Phake::when($this->builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($this->builder);

        $this->resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form = new UserType($this->class, $this->translator);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('user', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder, Phake::times(5))->add(Phake::anyParameters());
        Phake::verify($this->builder)->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test setDefaultOptions
     */
    public function testResolver()
    {
        $this->form->setDefaultOptions($this->resolver);

        Phake::verify($this->resolver)->setDefaults(Phake::anyParameters());
    }
}
