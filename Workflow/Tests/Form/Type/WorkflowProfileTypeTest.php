<?php
namespace OpenOrchestra\Workflow\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Workflow\Form\Type\WorkflowProfileType;

/**
 * Class WorkflowProfileTypeTest
 */
class WorkflowProfileTypeTest extends AbstractBaseTestCase
{
    /**
     * @var WorkflowProfileType
     */
    protected $form;
    protected $workflowProfileClass = 'workflow-profile-class';
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new WorkflowProfileType($this->workflowProfileClass, array());
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
        $this->assertSame('oo_workflow_profile', $this->form->getName());
    }
    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        $this->form->buildForm($builder, array());
        Phake::verify($builder, Phake::times(2))->add(Phake::anyParameters());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');
        $this->form->configureOptions($resolver);
        Phake::verify($resolver)->setDefaults(array(
            'data_class'    => $this->workflowProfileClass,
            'delete_button' => false,
            'new_button' => false,
            'group_enabled' => true,
            'group_render'  => array(
                'properties' => array(
                    'rank'  => 0,
                    'label' => 'open_orchestra_workflow_admin.form.workflow_profile.group.properties',
                ),
            ),
        ));
    }

    /**
     * Test build view
     */
    public function testBuildView()
    {
        $view = Phake::mock('Symfony\Component\Form\FormView');
        $form = Phake::mock('Symfony\Component\Form\Form');
        $options = array(
            'delete_button' => true,
            'new_button' => true,
        );
        $this->form->buildView($view, $form, $options);
        $this->assertTrue($view->vars['delete_button']);
        $this->assertTrue($view->vars['new_button']);
    }
}
