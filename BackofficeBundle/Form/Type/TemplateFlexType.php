<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class TemplateFlexType
 */
class TemplateFlexType extends AbstractType
{
    protected $templateClass;
    protected $areaClass;
    protected $translator;

    /**
     * @param string              $templateClass
     * @param string              $areaClass
     * @param TranslatorInterface $translator
     */
    public function __construct($templateClass, $areaClass, TranslatorInterface $translator)
    {
        $this->templateClass = $templateClass;
        $this->areaClass = $areaClass;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'open_orchestra_backoffice.form.template.name',
            ))
            ->add('templateId', 'hidden', array(
                'disabled' => true
            ));

        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->templateClass,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oo_template_flex';
    }
}
