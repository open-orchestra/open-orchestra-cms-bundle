<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueNodeSpecialPage
 */
class UniqueNodeSpecialPage extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.node.unique_node_special_page';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'unique_node_special_page';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
