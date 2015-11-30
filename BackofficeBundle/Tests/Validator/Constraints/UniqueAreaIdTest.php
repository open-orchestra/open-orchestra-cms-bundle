<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Validator\Constraints;

use OpenOrchestra\BackofficeBundle\Validator\Constraints\UniqueAreaId;
use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueAreaIdTest
 */
class UniqueAreaIdTest extends \PHPUnit_Framework_TestCase
{
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueAreaId();
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
        $this->assertSame('open_orchestra_backoffice_validators.area.unique_area_id', $this->constraint->message);
    }

    /**
     * Test validate by
     */
    public function testValidateBy()
    {
        $this->assertSame('unique_area_id', $this->constraint->validatedBy());
    }
}
