<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class GoogleMap
 */
class GoogleMapType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('latitude', 'text', array('constraints' => new NotBlank()))
            ->add('longitude', 'text', array('constraints' => new NotBlank()))
            ->add('zoom', 'text', array('constraints' => new NotBlank()));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_gmap';
    }

}
