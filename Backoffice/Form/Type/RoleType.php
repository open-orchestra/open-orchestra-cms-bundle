<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RoleType
 */
class RoleType extends AbstractType
{
    protected $backOfficeLanguages;
    protected $roleClass;

    /**
     * @param array  $backOfficeLanguages
     * @param string $roleClass
     */
    public function __construct(array $backOfficeLanguages, $roleClass)
    {
        $this->backOfficeLanguages = $backOfficeLanguages;
        $this->roleClass = $roleClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
            'label' => 'open_orchestra_backoffice.form.role.name',
        ));
        $builder->add('descriptions', 'oo_multi_languages', array(
            'label' => 'open_orchestra_backoffice.form.role.descriptions',
            'languages' => $this->backOfficeLanguages
        ));
        $builder->add('fromStatus', 'oo_status_choice', array(
            'embedded' => false,
            'label' => 'open_orchestra_backoffice.form.role.from_status',
            'required' => false,
        ));
        $builder->add('toStatus', 'oo_status_choice', array(
            'embedded' => false,
            'label' => 'open_orchestra_backoffice.form.role.to_status',
            'required' => false,
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
        $resolver->setDefaults(array(
            'data_class' => $this->roleClass,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_role';
    }

}
