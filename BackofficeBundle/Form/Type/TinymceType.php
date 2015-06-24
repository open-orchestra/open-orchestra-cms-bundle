<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TinymceType
 */
class TinymceType extends AbstractType
{
    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'tinymce';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'attr' => array('class' => 'tinymce')
        ));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'textarea';
    }
}
