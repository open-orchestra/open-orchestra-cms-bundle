<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Datetime;

/**
 * Class TrashcanRemoveValidator
 */
class TrashcanRemoveValidator extends ConstraintValidator
{
    /**
     * @param TrashItemInterface $trashItem
     * @param Constraint         $constraint
     */
    public function validate($trashItem, Constraint $constraint)
    {
        $dateDelete = $trashItem->getDeletedAt();
        $dateNow = new DateTime();
        if ((int)date_diff($dateDelete, $dateNow)->format('%a%') < 7) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
