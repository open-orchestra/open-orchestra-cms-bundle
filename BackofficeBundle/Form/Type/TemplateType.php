<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use OpenOrchestra\BackofficeBundle\EventSubscriber\AreaCollectionSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class TemplateType
 */
class TemplateType extends AbstractType
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
            ->add('boDirection', 'orchestra_direction', array(
                'label' => 'open_orchestra_backoffice.form.template.boDirection',
            ))
            ->add('templateId', 'hidden', array(
                'disabled' => true
            ));

        $builder->addEventSubscriber(new AreaCollectionSubscriber($this->areaClass, $this->translator));
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
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
        return 'template';
    }
}
