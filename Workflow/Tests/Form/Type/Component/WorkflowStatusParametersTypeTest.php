<?php

namespace OpenOrchestra\Workflow\Tests\Form\Type\Component;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Workflow\Form\Type\Component\WorkflowStatusParametersType;

/**
 * Class WorkflowStatusParametersTypeTest
 */
class WorkflowStatusParametersTypeTest extends AbstractBaseTestCase
{
    protected $form;
    protected $statusClass = 'status';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new WorkflowStatusParametersType($this->statusClass);
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
        $this->assertSame('oo_workflow_status_parameters', $this->form->getName());
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

        Phake::verify($builder, Phake::times(5))->add(Phake::anyParameters());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefault('data_class', $this->statusClass);
    }
}
