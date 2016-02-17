<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AbstractAreaFlexType
 */
abstract class AbstractAreaFlexType extends AbstractType
{
    protected $translator;
    protected $areaClass;

    /**
     * @param string              $areaClass
     * @param TranslatorInterface $translator
     */
    public function __construct($areaClass, TranslatorInterface $translator)
    {
        $this->areaClass = $areaClass;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', $this->areaClass);
    }
}
