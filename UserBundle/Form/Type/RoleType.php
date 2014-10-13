<?php

namespace PHPOrchestra\UserBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RoleType
 */
class RoleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
            'label' => 'php_orchestra_user.form.role.name',
        ));
        $builder->add('fromStatus', 'document',array(
            'class' => 'PHPOrchestra\ModelBundle\Document\Status',
            'label' => 'php_orchestra_user.form.role.from_status',
            'required' => false,
        ));
        $builder->add('toStatus', 'document',array(
            'class' => 'PHPOrchestra\ModelBundle\Document\Status',
            'label' => 'php_orchestra_user.form.role.to_status',
            'required' => false,
        ));

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PHPOrchestra\UserBundle\Document\Role',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'role';
    }

}
