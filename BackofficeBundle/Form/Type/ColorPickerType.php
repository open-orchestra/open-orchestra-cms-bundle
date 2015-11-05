<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ColorPicker
 */
class ColorPickerType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'attr' => array('class' => 'colorpicker')
        ));
    }

    /**
     * @return FormTypeInterface
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_color_picker';
    }

}
