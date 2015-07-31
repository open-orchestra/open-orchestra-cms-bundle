<?php

namespace OpenOrchestra\BackofficeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class BlockNodePattern
 */
class BlockNodePattern extends Constraint
{
    public $message = '';

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
