<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

@trigger_error('The '.__NAMESPACE__.'\EmbedKeyword class is deprecated since version 1.2.0 and will be removed in 2.0. use MultiLanguagesType', E_USER_DEPRECATED);

/**
 * Class TranslatedValueCollectionType
 *
 * @deprecated will be removed in 2.0, use MultiLanguagesType
 */
class TranslatedValueCollectionType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'type' => 'oo_translated_value',
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
        return 'oo_translated_value_collection';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'collection';
    }
}
