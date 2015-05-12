<?php

namespace OpenOrchestra\WorkflowAdminBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FonctionType
 */
class FonctionType extends AbstractType
{
    protected $fonctionClass;

    /**
     * @param string $fonctionClass
     */
    public function __construct($fonctionClass)
    {
        $this->fonctionClass = $fonctionClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'open_orchestra_workflow.form.fonction.name'
            ))
            ->add('roles', 'document', array(
                'class' => 'OpenOrchestra\ModelBundle\Document\Role',
                'property' => 'name',
                'label' => 'open_orchestra_workflow.form.fonction.role',
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
            'data_class' => $this->fonctionClass,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'fonction';
    }

}
