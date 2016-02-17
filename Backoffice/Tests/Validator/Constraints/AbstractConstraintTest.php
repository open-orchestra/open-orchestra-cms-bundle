<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Symfony\Component\Validator\Constraint;

/**
 * Class AbstractConstraintTest
 */
abstract class AbstractConstraintTest extends AbstractBaseTestCase
{
    protected $constraint;

    /**
     * @param Constraint $constraint
     * @param string     $validateBy
     * @param string     $target
     * @param string     $message
     */
    protected function assertConstraint($constraint, $validateBy, $target, $message)
    {
        $this->assertInstanceOf('Symfony\Component\Validator\Constraint', $constraint);
        $this->assertSame($validateBy, $constraint->validatedBy());
        $this->assertSame($target, $constraint->getTargets());
        $this->assertSame($message, $constraint->message);
    }
}
