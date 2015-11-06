<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class AbstractOrchestraGroupType
 */
abstract class AbstractOrchestraGroupType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'oo_orchestra_group';
    }
}
