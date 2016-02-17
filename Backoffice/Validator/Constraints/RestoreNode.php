<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class RestoreNode
 */
class RestoreNode extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.node.restore';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'restore_node';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
