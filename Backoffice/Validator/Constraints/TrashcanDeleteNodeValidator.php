<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Datetime;

/**
 * Class TrashcanDeleteNodeValidator
 */
class TrashcanDeleteNodeValidator extends ConstraintValidator
{
    /**
     * @param TrashItemInterface $trashItem
     * @param Constraint         $constraint
     */
    public function validate($trashItem, Constraint $constraint)
    {
        if ($trashItem->getType() === NodeInterface::TRASH_ITEM_TYPE) {
            $dateDelete = new DateTime($trashItem->getDeletedAt());
            $dateNow = new DateTime("now");
            if ((int)date_diff($dateDelete, $dateNow)->format('%a%') < 7) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
