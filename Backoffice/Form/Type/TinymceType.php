<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class TinymceType
 */
class TinymceType extends AbstractType
{
    protected $bbCodeTransformer;

    /**
     * @param DataTransformerInterface $BBcodeTransformer
     */
    public function __construct(DataTransformerInterface $BBcodeTransformer)
    {
        $this->bbCodeTransformer = $BBcodeTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->bbCodeTransformer);
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_tinymce';
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
