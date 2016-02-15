<?php

namespace OpenOrchestra\BackofficeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class AreaFlexRowLayoutValidator
 */
class AreaFlexRowLayoutValidator extends ConstraintValidator
{
    /**
     * @param string $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $columnsLayout = explode(',', $value);
        foreach ($columnsLayout as $columnWidth) {
            $columnWidth = trim($columnWidth);
            if (false === $this->validateColumnWidth($columnWidth)) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
                break;
            }
        }
    }

    /**
     * @param string $columnWidth
     *
     * @return bool
     */
    protected function validateColumnWidth($columnWidth)
    {
        $regex = "/^auto$|^\\d+(px|%)?$/";

        return 1 === preg_match_all($regex, $columnWidth);
    }
}
