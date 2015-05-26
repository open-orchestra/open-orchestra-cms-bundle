<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\WorkflowFunctionAdminBundle\Form\Type\WorkflowRightType;
use Phake;

/**
 * Description of WorkflowRightTypeTest
 */
class WorkflowRightTypeTest extends \PHPUnit_Framework_TestCase
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
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->workflowRightType->setDefaultOptions($resolverMock);

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
        $this->assertEquals('workflow_right', $this->workflowRightType->getName());
    }
}
