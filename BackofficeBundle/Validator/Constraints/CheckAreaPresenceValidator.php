<?php

namespace OpenOrchestra\BackofficeBundle\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CheckAreaPresenceValidator
 */
class CheckAreaPresenceValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param NodeInterface $value The value that should be validated
     * @param Constraint    $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (0 === $value->getAreas()->count()) {
            $this->context->buildViolation($constraint->message)
                          ->atPath('nodeSource')
                          ->addViolation();
            $this->context->buildViolation($constraint->message)
                          ->atPath('templateId')
                          ->addViolation();
        }
    }
}
