<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\EventListener\TranslateValueInitializerListener;
use OpenOrchestra\Backoffice\EventSubscriber\RoleTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RoleType
 */
class RoleType extends AbstractType
{
    protected $translateValueInitializer;
    protected $roleTypeSubscriber;
    protected $roleClass;

    /**
     * @param TranslateValueInitializerListener $translateValueInitializer
     * @param RoleTypeSubscriber                $roleTypeSubscriber
     * @param string                            $roleClass
     */
    public function __construct(TranslateValueInitializerListener $translateValueInitializer, RoleTypeSubscriber $roleTypeSubscriber, $roleClass)
    {
        $this->translateValueInitializer = $translateValueInitializer;
        $this->roleTypeSubscriber = $roleTypeSubscriber;
        $this->roleClass = $roleClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this->translateValueInitializer, 'preSetData'));

        $builder->add('name', null, array(
            'label' => 'open_orchestra_backoffice.form.role.name',
        ));
        $builder->add('descriptions', 'oo_translated_value_collection', array(
            'label' => 'open_orchestra_backoffice.form.role.descriptions'
        ));
        $builder->add('fromStatus', 'oo_status_choice', array(
            'embedded' => false,
            'label' => 'open_orchestra_backoffice.form.role.from_status',
            'required' => true,
        ));
        $builder->add('toStatus', 'oo_status_choice', array(
            'embedded' => false,
            'label' => 'open_orchestra_backoffice.form.role.to_status',
            'required' => true,
        ));

        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }

        $builder->addEventSubscriber($this->roleTypeSubscriber);
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
