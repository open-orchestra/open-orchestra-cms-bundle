<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\WorkflowFunctionAdminBundle\Form\Type\WorkflowFunctionType;
use Phake;

/**
 * Description of WorkflowFunctionTypeTest
 */
class WorkflowFunctionTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $workflowFunctionClass = 'fakeClass';
    protected $workflowFunctionType;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->workflowFunctionType = new WorkflowFunctionType($this->workflowFunctionClass);
    }

    /**
     * test default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->workflowFunctionType->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(
            array('data_class' => $this->workflowFunctionClass)
        );
    }

    /**
     * test buildForm
     */
    public function testBuildForm()
    {
        $formBuilderInterface = Phake::mock('Symfony\Component\Form\FormBuilderInterface');

        $this->workflowFunctionType->buildForm($formBuilderInterface, array());

        Phake::verify($formBuilderInterface, Phake::times(2))->add(Phake::anyParameters());
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('workflow_function', $this->workflowFunctionType->getName());
    }
}
