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
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Class GroupType
 */
class GroupType extends AbstractType
{
    protected $groupPerimeterSubscriber;
    protected $groupRoleTransformer;
    protected $groupClass;
    protected $backOfficeLanguages;
    protected $generatePerimeterManager;
    protected $eventDispatcher;
    protected $groupPerimeterTransformer;

    /**
     * @param EventSubscriberInterface $groupPerimeterSubscriber
     * @param EventDispatcherInterface $eventDispatcher
     * @param DataTransformerInterface $groupRoleTransformer
     * @param DataTransformerInterface $groupPerimeterTransformer
     * @param GeneratePerimeterManager $generatePerimeterManager
     * @param string                   $groupClass
     * @param array                    $backOfficeLanguages
     */
    public function __construct(
        EventSubscriberInterface $groupPerimeterSubscriber,
        EventDispatcherInterface $eventDispatcher,
        DataTransformerInterface $groupRoleTransformer,
        DataTransformerInterface $groupPerimeterTransformer,
        GeneratePerimeterManager $generatePerimeterManager,
        $groupClass,
        array $backOfficeLanguages
    ) {
        $this->groupPerimeterSubscriber = $groupPerimeterSubscriber;
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
        array_walk_recursive($configuration, function(&$path) {
            $path = GeneratePerimeterManager::changePathToName($path);
        });

        $builder
            ->add('name', 'text', array(
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
                'disabled' => !$options['new_button']
            ))
            ->add('roles', 'oo_group_role', array(
                'label' => false,
                'group_id' => 'right',
                'required' => false
            ))
            ->add('perimeters', 'oo_tree_list_collection', array(
                'label' => false,
                'configuration' => $configuration,
                'group_id' => 'perimeter',
                'required' => false
            ));
        $builder->get('roles')->addModelTransformer($this->groupRoleTransformer);
        $builder->get('perimeters')->addModelTransformer($this->groupPerimeterTransformer);

        $builder->addEventSubscriber($this->groupPerimeterSubscriber);
        $this->eventDispatcher->dispatch(GroupFormEvents::GROUP_FORM_CREATION, new GroupFormEvent($builder));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->groupClass,
                'delete_button' => false,
                'new_button' => false,
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
                    )
                ),
                'sub_group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_group.form.group.sub_group.property',
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
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['delete_button'] = $options['delete_button'];
        $view->vars['new_button'] = $options['new_button'];
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
