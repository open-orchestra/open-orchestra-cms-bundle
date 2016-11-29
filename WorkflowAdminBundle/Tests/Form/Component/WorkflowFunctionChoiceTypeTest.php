<?php

namespace OpenOrchestra\WorkflowAdminBundle\Tests\Form\Type\Component;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\WorkflowAdminBundle\Form\Type\Component\WorkflowFunctionChoiceType;
use Phake;

/**
 * Description of WorkflowFunctionChoiceTypeTest
 */
class WorkflowFunctionChoiceTypeTest extends AbstractBaseTestCase
{
    protected $workflowFunctionClass = 'fakeClass';
    protected $orchestraWorkflowFunction;
    protected $multiLanguagesManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->multiLanguagesManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface');
        $this->orchestraWorkflowFunction = new WorkflowFunctionChoiceType($this->workflowFunctionClass, $this->multiLanguagesManager);
    }

    /**
     * test default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->orchestraWorkflowFunction->configureOptions($resolverMock);
        $multiLanguagesManager = $this->multiLanguagesManager;

        Phake::verify($resolverMock)->setDefaults(
            array(
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'class' => $this->workflowFunctionClass,
                'choice_label' => function (WorkflowFunctionInterface $choice) use ($multiLanguagesManager) {
                    return $multiLanguagesManager->choose($choice->getNames());
                },
            )
        );
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertEquals('document', $this->orchestraWorkflowFunction->getParent());
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_workflow_function_choice', $this->orchestraWorkflowFunction->getName());
    }
}
