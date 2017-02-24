<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\ModelInterface\Exceptions\MainAliasNotExisting;

/**
 * Class CheckMainAliasPresenceValidator
 */
class CheckMainAliasPresenceValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param SiteInterface $value The value that should be validated
     * @param Constraint    $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        try {
            $value->getMainAlias();
        } catch (MainAliasNotExisting $exception) {
            foreach ($value->getAliases() as $name => $siteAlias) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('aliases['.$name.'].main')
                    ->addViolation();
            }
        }
    }
}
