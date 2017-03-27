<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueBlockCode
 */
class UniqueBlockCode extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.block.unique_code';
    public $block;

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'unique_block_code';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
