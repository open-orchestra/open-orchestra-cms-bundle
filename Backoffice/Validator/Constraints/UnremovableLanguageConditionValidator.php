<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


/**
 * Class UnremovableLanguageConditionValidator
 */
class UnremovableLanguageConditionValidator extends ConstraintValidator
{
    /**
     * @param string     $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        foreach ($value as $alias) {
            $languages[] = $alias->getLanguage();
        }
        $languages = array_unique($languages);

        if (count(array_diff($constraint->getLanguages(), $languages)) > 0) {
            $this->context->buildViolation($constraint->message)
                ->atPath('aliases')
                ->addViolation();
        }
    }
}
