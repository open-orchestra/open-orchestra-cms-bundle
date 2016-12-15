<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class GroupType
 */
class GroupType extends AbstractType
{
    protected $groupMemberSubscriber;
    protected $groupClass;
    protected $backOfficeLanguages;

    /**
     * @param EventSubscriberInterface $groupMemberSubscriber
     * @param string                   $groupClass
     * @param array                    $backOfficeLanguages
     */
    public function __construct(
        EventSubscriberInterface $groupMemberSubscriber,
        $groupClass,
        array $backOfficeLanguages
    ) {
        $this->groupMemberSubscriber = $groupMemberSubscriber;
        $this->groupClass = $groupClass;
        $this->backOfficeLanguages = $backOfficeLanguages;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'open_orchestra_group.form.group.name',
                'group_id' => 'property',
                'sub_group_id' => 'property',
            ))
            ->add('labels', 'oo_multi_languages', array(
                'label' => 'open_orchestra_group.form.group.label',
                'languages' => $this->backOfficeLanguages,
                'group_id' => 'property',
                'sub_group_id' => 'property',
            ))
            ->add('site', 'oo_group_site_choice', array(
                'label' => 'open_orchestra_group.form.group.site',
                'group_id' => 'property',
                'sub_group_id' => 'property',
            ));
        $builder->addEventSubscriber($this->groupMemberSubscriber);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->groupClass,
                'group_enabled' => true,
                'group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_group.form.group.group.property',
                    ),
                    'right' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_group.form.group.group.right',
                    ),
                    'perimeter' => array(
                        'rank' => 3,
                        'label' => 'open_orchestra_group.form.group.group.perimeter',
                    ),
                    'member' => array(
                        'rank' => 4,
                        'label' => 'open_orchestra_group.form.group.group.member',
                    ),
                ),
                'sub_group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_group.form.group.sub_group.property',
                    ),
                    'contribution' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_group.form.group.sub_group.contribution',
                    ),
                    'administration' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_group.form.group.sub_group.administration',
                    ),
                    'page' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_group.form.group.sub_group.page',
                    ),
                ),
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_group';
    }

}
