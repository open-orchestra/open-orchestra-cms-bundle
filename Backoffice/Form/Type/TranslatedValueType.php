<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\EventSubscriber\TranslatedValueTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

@trigger_error('The '.__NAMESPACE__.'\EmbedKeyword class is deprecated since version 1.2.0 and will be removed in 2.0. use MultiLanguagesType', E_USER_DEPRECATED);

/**
 * Class TranslatedValueType
 *
 * @deprecated will be removed in 2.0, use MultiLanguagesType
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
