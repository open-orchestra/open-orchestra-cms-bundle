<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class AbstractConstraintTest
 */
abstract class AbstractConstraintTest extends \PHPUnit_Framework_TestCase
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
