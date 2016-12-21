<?php

namespace OpenOrchestra\Workflow\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Workflow\Form\Type\StatusType;

/**
 * Class StatusTypeTest
 */
class StatusTypeTest extends AbstractBaseTestCase
{
    /**
     * @var StatusType
     */
    protected $form;

    protected $statusClass = 'status';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new StatusType($this->statusClass, array());
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
        $this->assertSame('oo_status', $this->form->getName());
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
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'data_class'    => $this->statusClass,
            'group_enabled' => true,
            'group_render'  => array(
                'properties' => array(
                    'rank'  => 0,
                    'label' => 'open_orchestra_workflow_admin.form.status.group.properties',
                ),
            ),
        ));
    }
}
