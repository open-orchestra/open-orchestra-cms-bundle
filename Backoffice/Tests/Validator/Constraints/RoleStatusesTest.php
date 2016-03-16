<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\RoleStatuses;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Symfony\Component\Validator\Constraint;

/**
 * Class RoleStatusesTest
 */
class RoleStatusesTest extends AbstractBaseTestCase
{
    /**
     * @var RoleStatuses
     */
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new RoleStatuses();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\Constraint', $this->constraint);
    }

    /**
     * Test target
     */
    public function testTarget()
    {
        $this->assertSame(Constraint::CLASS_CONSTRAINT, $this->constraint->getTargets());
    }

    /**
     * Test messages
     */
    public function testMessages()
    {
        $this->assertSame('open_orchestra_backoffice_validators.role.duplicate_statuses', $this->constraint->message);
    }

    /**
     * Test validate by
     */
    public function testValidateBy()
    {
        $this->assertSame('role_statuses', $this->constraint->validatedBy());
    }
}
