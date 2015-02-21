<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\TranslatedValueTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TranslatedValueType
 */
class TranslatedValueType extends AbstractType
{
    protected $translatedValueClass;

    /**
     * @param string $translatedValueClass
     */
    public function __construct($translatedValueClass)
    {
        $this->translatedValueClass = $translatedValueClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('language', 'hidden');
        $builder->addEventSubscriber(new TranslatedValueTypeSubscriber());
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'translated_value';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->translatedValueClass
        ));
    }
}
