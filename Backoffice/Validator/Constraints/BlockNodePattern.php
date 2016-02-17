<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class BlockNodePattern
 */
class BlockNodePattern extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.node.pattern';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'block_node_pattern';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
