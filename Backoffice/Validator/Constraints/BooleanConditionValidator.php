<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\ModelInterface\Form\DataTransformer\ConditionFromBooleanToBddTransformerInterface;

/**
 * Class BooleanConditionValidator
 */
class BooleanConditionValidator extends ConstraintValidator
{
    /**
     * @param string $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (false === $this->validateCondition($value)) {
            $this->context->buildViolation($constraint->message)
                 ->addViolation();
        }
    }

    /**
     * @param string $columnWidth
     *
     * @return bool
     */
    protected function validateCondition($condition)
    {
        $is_boolean = true;
        $encapsuledElements = array();
        preg_match_all(
            ConditionFromBooleanToBddTransformerInterface::GET_BALANCED_BRACKETS,
            $condition,
            $encapsuledElements
        );
        foreach ($encapsuledElements[0] as $key => $encapsuledElement) {
            $is_boolean = $is_boolean &&
                (preg_match(ConditionFromBooleanToBddTransformerInterface::IS_AND_BOOLEAN, $encapsuledElements[1][$key])
                || preg_match(ConditionFromBooleanToBddTransformerInterface::IS_OR_BOOLEAN, $encapsuledElements[1][$key]));
            $condition = preg_replace('/'.preg_quote($encapsuledElement).'/', '##', $condition, 1);
            if (count($encapsuledElements[0]) > 0) {
                $is_boolean = $is_boolean  && $this->validateCondition($condition);
            }
        }

        return $is_boolean;
    }
}
