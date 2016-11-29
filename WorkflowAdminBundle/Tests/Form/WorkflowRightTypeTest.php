<?php

namespace OpenOrchestra\WorkflowAdminBundle\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\WorkflowAdminBundle\Form\Type\WorkflowRightType;
use Phake;

/**
 * Description of WorkflowRightTypeTest
 */
class WorkflowRightTypeTest extends AbstractBaseTestCase
{
    protected $workflowRightClass = 'fakeClass';
    protected $workflowRightType;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->workflowRightType = new WorkflowRightType($this->workflowRightClass);
    }

    /**
     * test default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->workflowRightType->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(
            array('data_class' => $this->workflowRightClass)
        );
    }

    /**
     * test buildForm
     */
    public function testBuildForm()
    {
        $formBuilderInterface = Phake::mock('Symfony\Component\Form\FormBuilderInterface');

        $this->workflowRightType->buildForm($formBuilderInterface, array());

        Phake::verify($formBuilderInterface, Phake::times(1))->add(Phake::anyParameters());
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_workflow_right', $this->workflowRightType->getName());
    }
}
