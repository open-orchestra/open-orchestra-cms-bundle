<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;
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
        if ($constraint instanceof UnremovableLanguageConditionInterface) {
            $languages = array();
            foreach ($value as $alias) {
                if ($alias instanceof SiteAliasInterface) {
                    $languages[] = $alias->getLanguage();
                }
            }

            $languages = array_unique($languages);
            if (count(array_diff($constraint->getLanguages(), $languages)) > 0) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('aliases')
                    ->addViolation();
            }
        }
    }
}
