<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AreaCollectionSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\TemplateTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TemplateType
 */
class TemplateType extends AbstractType
{
    protected $templateClass;

    /**
     * @param string $templateClass
     */
    public function __construct($templateClass)
    {
        $this->templateClass = $templateClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('language', 'orchestra_language')
            ->add('status', 'document', array(
                'class' => 'PHPOrchestra\ModelBundle\Document\Status',
                'property' => 'name'
            ))
            ->add('boDirection', 'orchestra_direction');

        $builder->addEventSubscriber(new TemplateTypeSubscriber());
        $builder->addEventSubscriber(new AreaCollectionSubscriber());
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
