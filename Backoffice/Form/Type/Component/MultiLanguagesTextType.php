<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MultiLanguagesTextType
 */
class MultiLanguagesTextType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'label_attr' => array('class' => 'translated-value'),
            'languages' => array('en'),
            'type' => 'text'
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->getModelTransformers() as $transformer) {
            $builder->addModelTransformer($transformer);
        }

        foreach ($this->getViewTransformers() as $transformer) {
            $builder->addViewTransformer($transformer);
        }

        foreach ($options['languages'] as $language) {
            $builder->add($language, $options['type'], array('label' => $language));
        }
    }

    /**
     * Return array<Symfony\Component\Form\DataTransformerInterface>
     */
    protected function getModelTransformers()
    {
        return array();
    }

    /**
     * Return array<Symfony\Component\Form\DataTransformerInterface>
     */
    protected function getViewTransformers()
    {
        return array();
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_multi_languages_text';
    }
}
