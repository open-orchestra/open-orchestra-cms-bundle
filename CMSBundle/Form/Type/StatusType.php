<?php

namespace PHPOrchestra\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PHPOrchestra\CMSBundle\Form\Type\OrchestraChoiceType;

/**
 * Class StatusType
 */
class StatusType extends OrchestraChoiceType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'orchestra_status';
    }
}
