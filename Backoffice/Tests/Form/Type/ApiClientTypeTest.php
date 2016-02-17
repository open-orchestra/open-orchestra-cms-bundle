<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\Backoffice\Form\Type\ApiClientType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class UserTypeTest
 */
class ApiClientTypeTest extends AbstractBaseTestCase
{
    protected $builder;
    protected $resolver;

    /**
     * @var ApiClientType
     */
    protected $form;

    protected $class = 'OpenOrchestra\UserBundle\Document\ApiClient';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->builder)->add(Phake::anyParameters())->thenReturn($this->builder);
        Phake::when($this->builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($this->builder);

        $this->resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');
        $this->form = new ApiClientType($this->class);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('oo_api_client', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder, Phake::times(5))->add(Phake::anyParameters());
    }

    /**
     * Test configureOptions
     */
    public function testResolver()
    {
        $this->form->configureOptions($this->resolver);

        Phake::verify($this->resolver)->setDefaults(Phake::anyParameters());
    }
}
