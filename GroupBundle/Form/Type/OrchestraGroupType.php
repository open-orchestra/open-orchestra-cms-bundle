<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\Type\AbstractOrchestraGroupType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class OrchestraGroupType
 */
class OrchestraGroupType extends AbstractOrchestraGroupType
{
    protected $groupClass;

    /**
     * @param string $groupClass
     */
    public function __construct($groupClass)
    {
        $this->groupClass = $groupClass;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'class' => $this->groupClass,
            'property' => 'name'
        ));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'document';
    }
}
