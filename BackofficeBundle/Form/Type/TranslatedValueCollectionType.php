<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TranslatedValueCollectionType
 */
class TranslatedValueCollectionType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'type' => 'translated_value',
            'allow_add' => false,
            'allow_delete' => false,
            'label_attr' => array('class' => 'translated-value'),
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'translated_value_collection';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'collection';
    }
}
