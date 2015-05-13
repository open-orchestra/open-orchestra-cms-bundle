<?php

namespace OpenOrchestra\WorkflowFonctionAdminBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class WorkflowFonctionType
 */
class WorkflowFonctionType extends AbstractType
{
    protected $workflowFonctionClass;

    /**
     * @param string $workflowFonctionClass
     */
    public function __construct($workflowFonctionClass)
    {
        $this->workflowFonctionClass = $workflowFonctionClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'open_orchestra_workflowfonction.form.workflowfonction.name'
            ))
            ->add('roles', 'document', array(
                'class' => 'OpenOrchestra\ModelBundle\Document\Role',
                'property' => 'name',
                'label' => 'open_orchestra_workflowfonction.form.workflowfonction.role',
                'multiple' => true
            ));
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->workflowFonctionClass,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'workflowfonction';
    }

}
