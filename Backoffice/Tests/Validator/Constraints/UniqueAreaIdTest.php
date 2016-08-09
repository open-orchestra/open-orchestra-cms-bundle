<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\UniqueAreaId;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Symfony\Component\Validator\Constraint;
@trigger_error('The '.__NAMESPACE__.'\ChoiceArrayToStringTransformer class is deprecated since version 1.2.0 and will be removed in 2.0', E_USER_DEPRECATED);

/**
 * Class UniqueAreaIdTest
 * @deprecated will be removed in 2.0
 */
class UniqueAreaIdTest extends AbstractBaseTestCase
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
