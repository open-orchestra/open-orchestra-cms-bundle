<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\EventSubscriber\TranslatedValueTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TranslatedValueType
 */
class TranslatedValueType extends AbstractType
{
    protected $translatedValueClass;
    protected $languages;
    
    /**
     * @param string $translatedValueClass
     * @param array  $languages
     */
    public function __construct($translatedValueClass, $languages)
    {
        $this->translatedValueClass = $translatedValueClass;
        $this->languages = $languages;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new TranslatedValueTypeSubscriber($this->languages));
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_translated_value';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->translatedValueClass
        ));
    }
}
