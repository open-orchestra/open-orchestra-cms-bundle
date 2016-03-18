<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class TrashcanDeleteNode
 */
class TrashcanDeleteNode extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.trashitem.delete_node_date';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'delete_node_date';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
