<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class AbstractGroupChoiceType
 */
abstract class AbstractGroupChoiceType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'oo_group_choice';
    }
}
