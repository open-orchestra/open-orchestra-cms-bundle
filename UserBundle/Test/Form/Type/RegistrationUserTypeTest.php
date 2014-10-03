<?php

namespace PHPOrchestra\UserBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\UserBundle\Form\Type\RegistrationUserType;

/**
 * Class RegistrationUserTypeTest
 */
class RegistrationUserTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $form;

    protected $translator;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn('string');

        $this->form = new RegistrationUserType('PHPOrchestra\UserBundle\Document\User', $this->translator);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('registration_user', $this->form->getName());
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

        Phake::verify($builder, Phake::times(6))->add(Phake::anyParameters());
        Phake::verify($builder)->addEventSubscriber(Phake::anyParameters());
    }
}
