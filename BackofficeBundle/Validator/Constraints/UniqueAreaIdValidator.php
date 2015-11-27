<?php

namespace OpenOrchestra\BackofficeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\ModelInterface\Model\AreaInterface;

/**
 * Class UniqueAreaIdValidator
 */
class UniqueAreaIdValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param AreaInterface              $value      The value that should be validated
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
