<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CheckVariableRoutePatternInMenuValidator
 */
class CheckVariableRoutePatternInMenuValidator extends ConstraintValidator
{
    /**
     * Checks if a dynamic route pattern isn't in menu
     *
     * @param NodeInterface $value      The value that should be validated
     * @param Constraint    $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value->isInMenu() || $value->isInFooter()) {
            if (preg_match('/{.*}/', $value->getRoutePattern())) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('routePattern')
                    ->addViolation();
            }
        }
    }
}
