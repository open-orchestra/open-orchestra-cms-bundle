<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AreaCollectionSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TemplateType
 */
class TemplateType extends AbstractType
{
    protected $templateClass;
    protected $areaClass;

    /**
     * @param string $templateClass
     * @param string $areaClass
     */
    public function __construct($templateClass, $areaClass)
    {
        $this->templateClass = $templateClass;
        $this->areaClass = $areaClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'php_orchestra_backoffice.form.template.name',
            ))
            ->add('language', 'orchestra_language', array(
                'label' => 'php_orchestra_backoffice.form.template.language',
            ))
            ->add('boDirection', 'orchestra_direction', array(
                'label' => 'php_orchestra_backoffice.form.template.boDirection',
            ))
            ->add('templateId', 'hidden', array(
                'disabled' => true
            ));

        $builder->addEventSubscriber(new AreaCollectionSubscriber($this->areaClass));
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
