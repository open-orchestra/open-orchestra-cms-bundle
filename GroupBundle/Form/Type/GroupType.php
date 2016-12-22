<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\DataTransformerInterface;
use OpenOrchestra\GroupBundle\Event\GroupFormEvent;
use OpenOrchestra\GroupBundle\GroupFormEvents;
use OpenOrchestra\Backoffice\GeneratePerimeter\GeneratePerimeterManager;

/**
 * Class GroupType
 */
class GroupType extends AbstractType
{
    protected $groupMemberSubscriber;
    protected $groupRoleTransformer;
    protected $groupClass;
    protected $backOfficeLanguages;

    /**
     * @param EventSubscriberInterface $groupMemberSubscriber
     * @param EventDispatcherInterface $eventDispatcher
     * @param DataTransformerInterface $groupRoleTransformer
     * @param DataTransformerInterface $groupPerimeterTransformer
     * @param GeneratePerimeterManager $generatePerimeterManager
     * @param string                   $groupClass
     * @param array                    $backOfficeLanguages
     */
    public function __construct(
        EventSubscriberInterface $groupMemberSubscriber,
        EventDispatcherInterface $eventDispatcher,
        DataTransformerInterface $groupRoleTransformer,
        DataTransformerInterface $groupPerimeterTransformer,
        GeneratePerimeterManager $generatePerimeterManager,
        $groupClass,
        array $backOfficeLanguages
    ) {
        $this->groupMemberSubscriber = $groupMemberSubscriber;
        $this->eventDispatcher = $eventDispatcher;
        $this->groupRoleTransformer = $groupRoleTransformer;
        $this->groupPerimeterTransformer = $groupPerimeterTransformer;
        $this->generatePerimeterManager = $generatePerimeterManager;
        $this->groupClass = $groupClass;
        $this->backOfficeLanguages = $backOfficeLanguages;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $configuration = $this->generatePerimeterManager->getPerimetersConfiguration();
        array_walk_recursive($configuration, function(&$item) {
            $item = str_replace('/', '::', $item);
        });

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
            ))
            ->add('roles', 'oo_group_role', array(
                'label' => false,
                'group_id' => 'right',
                'sub_group_id' => 'right',
            ))
            ->add('perimeters', 'oo_tree_list_collection', array(
                'label' => false,
                'configuration' => $configuration,
                'group_id' => 'perimeter',
                'sub_group_id' => 'page',
            ));
        $builder->get('roles')->addModelTransformer($this->groupRoleTransformer);
        $builder->get('perimeters')->addModelTransformer($this->groupPerimeterTransformer);
        $builder->addEventSubscriber($this->groupMemberSubscriber);
        $this->eventDispatcher->dispatch(GroupFormEvents::GROUP_FORM_CREATION, new GroupFormEvent($builder, $this));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
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
                    'right' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_group.form.group.sub_group.right',
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
