<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueMainAliasValidator
 */
class UniqueMainAliasValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param SiteInterface              $value      The value that should be validated
     * @param UniqueMainAlias|Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value->getAliases()->filter(function(SiteAliasInterface $alias) {
            return $alias->isMain();
        })->count() > 1) {
            $this->context->buildViolation($constraint->message)
                ->atPath('aliases')
                ->addViolation();
        }
    }

}
