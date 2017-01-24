<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use OpenOrchestra\Backoffice\EventSubscriber\ContentTypeStatusableSubscriber;
use OpenOrchestra\Backoffice\EventSubscriber\ContentTypeTypeSubscriber;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Class ContentTypeType
 */
class ContentTypeType extends AbstractType
{
    protected $contentTypeClass;
    protected $backOfficeLanguages;
    protected $translator;
    protected $contentTypeTypeSubscriber;
    protected $contentTypeStatusableSubscriber;

    /**
     * @param string                          $contentTypeClass
     * @param TranslatorInterface             $translator
     * @param array                           $backOfficeLanguages
     * @param ContentTypeTypeSubscriber       $contentTypeTypeSubscriber,
     * @param ContentTypeStatusableSubscriber $contentTypeStatusableSubscriber
     */
    public function __construct(
        $contentTypeClass,
        TranslatorInterface $translator,
        array $backOfficeLanguages,
        ContentTypeTypeSubscriber $contentTypeTypeSubscriber,
        ContentTypeStatusableSubscriber $contentTypeStatusableSubscriber
    ) {
        $this->contentTypeClass = $contentTypeClass;
        $this->translator = $translator;
        $this->backOfficeLanguages = $backOfficeLanguages;
        $this->contentTypeTypeSubscriber = $contentTypeTypeSubscriber;
        $this->contentTypeStatusableSubscriber = $contentTypeStatusableSubscriber;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('names', 'oo_multi_languages', array(
                'label' => 'open_orchestra_backoffice.form.content_type.names',
                'languages' => $this->backOfficeLanguages,
                'group_id' => 'property',
                'sub_group_id' => 'property',
            ))
            ->add('contentTypeId', 'text', array(
                'label' => 'open_orchestra_backoffice.form.content_type.content_type_id',
                'group_id' => 'property',
                'sub_group_id' => 'property',
                'attr' => array(
                    'class' => 'generate-id-dest'
                )
            ))
            ->add('definingStatusable', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.content_type.defining_statusable.label',
                'required' => false,
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.content_type.defining_statusable.helper'),
                'group_id' => 'property',
                'sub_group_id' => 'property',
            ))
            ->add('definingVersionable', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.content_type.defining_versionable.label',
                'required' => false,
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.content_type.defining_versionable.helper'),
                'group_id' => 'property',
                'sub_group_id' => 'property',
            ))
            ->add('template', 'text', array(
                'label' => 'open_orchestra_backoffice.form.content_type.template.label',
                'required' => false,
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.content_type.template.helper'),
                'group_id' => 'property',
                'sub_group_id' => 'customization',
            ))
            ->add('linkedToSite', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.content_type.linked_to_site',
                'required' => false,
                'group_id' => 'property',
                'sub_group_id' => 'share',
            ))
            ->add('alwaysShared', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.content_type.always_shared',
                'required' => false,
                'group_id' => 'property',
                'sub_group_id' => 'share',
            ))
            ->add('defaultListable', 'collection', array(
                'required' => false,
                'type' => 'oo_default_listable_checkbox',
                'label' => false,
                'group_id' => 'property',
                'sub_group_id' => 'visible',
            ))
            ->add('version', 'text', array(
                'label' => 'open_orchestra_backoffice.form.content_type.version',
                'required' => false,
                'disabled' => true,
                'group_id' => 'property',
                'sub_group_id' => 'version',
            ))
            ->add('fields', 'collection', array(
                'type' => 'oo_field_type',
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'sortable' => true,
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('open_orchestra_backoffice.form.field_type.add'),
                    'data-prototype-label-new' => $this->translator->trans('open_orchestra_backoffice.form.field_type.new'),
                    'data-prototype-label-remove' => $this->translator->trans('open_orchestra_backoffice.form.field_type.delete'),
                ),
                'options' => array( 'label' => false ),
                'group_id' => 'field',
            ));

        $builder->addEventSubscriber($this->contentTypeTypeSubscriber);
        $builder->addEventSubscriber($this->contentTypeStatusableSubscriber);
        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->contentTypeClass,
                'delete_button' => false,
                'new_button' => false,
                'group_enabled' => true,
                'group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.content_type.group.property',
                    ),
                    'field' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.content_type.group.field',
                    ),
                ),
                'sub_group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.content_type.sub_group.property',
                    ),
                    'customization' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.content_type.sub_group.customization',
                    ),
                    'share' => array(
                        'rank' => 2,
                        'label' => 'open_orchestra_backoffice.form.content_type.sub_group.share',
                    ),
                    'visible' => array(
                        'rank' => 3,
                        'label' => 'open_orchestra_backoffice.form.content_type.sub_group.visible',
                    ),
                    'version' => array(
                        'rank' => 4,
                        'label' => 'open_orchestra_backoffice.form.content_type.sub_group.version',
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
     * @return string
     */
    public function getName()
    {
        return 'oo_content_type';
    }
}
