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
        $mainAliases = $value->getAliases()->filter(function(SiteAliasInterface $alias) {
            return $alias->isMain();
        });
        if ($mainAliases->count() > 1) {
            foreach ($mainAliases as $name => $siteAlias) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('aliases[' . $name . '].main')
                    ->addViolation();
            }
        }
    }

}
