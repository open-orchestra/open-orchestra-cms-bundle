<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use OpenOrchestra\Backoffice\EventSubscriber\ContentTypeStatusableSubscriber;
use OpenOrchestra\Backoffice\EventSubscriber\ContentTypeTypeSubscriber;

/**
 * Class ContentTypeType
 */
class ContentTypeType extends AbstractType
{
    protected $contentTypeClass;
    protected $backOfficeLanguages;
    protected $translator;
    protected $contentTypeOrderFieldTransformer;
    protected $contentTypeTypeSubscriber;
    protected $contentTypeStatusableSubscriber;

    /**
     * @param string                          $contentTypeClass
     * @param TranslatorInterface             $translator
     * @param array                           $backOfficeLanguages
     * @param DataTransformerInterface        $contentTypeOrderFieldTransformer
     * @param ContentTypeTypeSubscriber       $contentTypeTypeSubscriber,
     * @param ContentTypeStatusableSubscriber $contentTypeStatusableSubscriber
     */
    public function __construct(
        $contentTypeClass,
        TranslatorInterface $translator,
        array $backOfficeLanguages,
        DataTransformerInterface $contentTypeOrderFieldTransformer,
        ContentTypeTypeSubscriber $contentTypeTypeSubscriber,
        ContentTypeStatusableSubscriber $contentTypeStatusableSubscriber
    ) {
        $this->contentTypeClass = $contentTypeClass;
        $this->translator = $translator;
        $this->backOfficeLanguages = $backOfficeLanguages;
        $this->contentTypeOrderFieldTransformer = $contentTypeOrderFieldTransformer;
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
            ->add('contentTypeId', 'text', array(
                'label' => 'open_orchestra_backoffice.form.content_type.content_type_id',
                'attr' => array(
                    'class' => 'generate-id-dest'
                )
            ))
            ->add('names', 'oo_multi_languages', array(
                'label' => 'open_orchestra_backoffice.form.content_type.names',
                'languages' => $this->backOfficeLanguages
            ))
            ->add('template', 'text', array(
                'label' => 'open_orchestra_backoffice.form.content_type.template.label',
                'required' => false,
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.content_type.template.helper'),
            ))
            ->add('linkedToSite', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.content_type.linked_to_site',
                'required' => false,
            ))
            ->add('definingStatusable', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.content_type.defining_statusable.label',
                'required' => false,
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.content_type.defining_statusable.helper'),
            ))
            ->add('definingVersionable', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.content_type.defining_versionable.label',
                'required' => false,
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.content_type.defining_versionable.helper'),
            ))
            ->add('defaultListable', 'collection', array(
                'required' => false,
                'type' => 'oo_default_listable_checkbox',
                'label' => 'open_orchestra_backoffice.form.content_type.default_display',
            ))
            ->add('fields', 'bootstrap_collection', array(
                'type' => 'oo_field_type',
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'open_orchestra_backoffice.form.content_type.fields',
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('open_orchestra_backoffice.form.field_type.add'),
                    'data-prototype-label-new' => $this->translator->trans('open_orchestra_backoffice.form.field_type.new'),
                    'data-prototype-label-remove' => $this->translator->trans('open_orchestra_backoffice.form.field_type.delete'),
                ),
                'options' => array( 'label' => false ),
            ));

        $builder->addEventSubscriber($this->contentTypeTypeSubscriber);
        $builder->addEventSubscriber($this->contentTypeStatusableSubscriber);
        $builder->get('fields')->addModelTransformer($this->contentTypeOrderFieldTransformer);
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
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oo_content_type';
    }
}
