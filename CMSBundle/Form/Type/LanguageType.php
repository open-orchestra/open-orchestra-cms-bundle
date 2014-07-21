<?php

namespace PHPOrchestra\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PHPOrchestra\CMSBundle\Form\Type\OrchestraChoiceType;

/**
 * Class LanguageType
 */
class LanguageType extends OrchestraChoiceType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'orchestra_language';
    }
}
