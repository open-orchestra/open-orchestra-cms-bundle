<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
@trigger_error('The '.__NAMESPACE__.'\ChoiceArrayToStringTransformer class is deprecated since version 1.2.0 and will be removed in 2.0', E_USER_DEPRECATED);

/**
 * Class UniqueAreaIdValidator
 * @deprecated will be removed in 2.0
 */
class UniqueAreaIdValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param AreaInterface           $value      The value that should be validated
     * @param UniqueAreaId|Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        $areasId = [];
        $subareas = $value->getAreas();
        foreach ($subareas as $area) {
            $areaId = $area->getAreaId();
            if (in_array($areaId, $areasId)){
                $this->context->buildViolation($constraint->message)
                    ->atPath("newAreas")
                    ->addViolation();
            }
            $areasId[] = $areaId;
        }
    }
}
