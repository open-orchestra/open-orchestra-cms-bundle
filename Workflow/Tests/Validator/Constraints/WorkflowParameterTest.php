<?php

namespace OpenOrchestra\Workflow\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\Workflow\Validator\Constraints\WorkflowParameter;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;

/**
 * Class WorkflowParameterTest
 */
class WorkflowParameterTest extends AbstractBaseTestCase
{
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new WorkflowParameter();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\Constraint', $this->constraint);
    }

    /**
     * Test validatedBy
     */
    public function testValidatedBy()
    {
        $this->assertSame('workflow_parameters', $this->constraint->validatedBy());
    }

    /**
     * Test getTargets
     */
    public function testGetTargets()
    {
        $this->assertSame(Constraint::CLASS_CONSTRAINT, $this->constraint->getTargets());
    }

    /**
     * Test messages
     */
    public function testMessages()
    {
        $this->assertSame(
            'open_orchestra_workflow_admin_validators.workflow_parameters.required',
            $this->constraint->requiredParameterMessage
        );
        $this->assertSame(
            'open_orchestra_workflow_admin_validators.workflow_parameters.unique',
            $this->constraint->uniqueParameterMessage
        );
    }
}
