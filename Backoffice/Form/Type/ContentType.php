<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenOrchestra\Backoffice\Event\ContentFormEvent;
use OpenOrchestra\Backoffice\ContentFormEvents;

/**
 * Class ContentType
 */
class ContentType extends AbstractType
{
    protected $contentTypeSubscriber;
    protected $statusableChoiceStatusSubscriber;
    protected $eventDispatcher;
    protected $contentClass;

    /**
     * @param EventSubscriberInterface $contentTypeSubscriber
     * @param EventSubscriberInterface $statusableChoiceStatusSubscriber
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $contentClass
     */
    public function __construct(
        EventSubscriberInterface $contentTypeSubscriber,
        EventSubscriberInterface $statusableChoiceStatusSubscriber,
        EventDispatcherInterface $eventDispatcher,
        $contentClass
    ) {
        $this->contentTypeSubscriber = $contentTypeSubscriber;
        $this->statusableChoiceStatusSubscriber = $statusableChoiceStatusSubscriber;
        $this->eventDispatcher = $eventDispatcher;
        $this->contentClass = $contentClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', 'text', array(
            'label' => 'open_orchestra_backoffice.form.content.name',
            'disabled' => $options['is_blocked_edition'],
            'group_id' => 'property',
            'sub_group_id' => 'information',
        ))
        ->add('keywords', 'oo_keywords_choice', array(
                'label' => 'open_orchestra_backoffice.form.content.keywords',
                'required' => false,
                'group_id' => 'property',
                'sub_group_id' => 'information',
            ));
        if ($options['need_link_to_site_defintion']) {
            $builder->add('linkedToSite', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.content.linked_to_site',
                'required' => false,
                'group_id' => 'property',
                'sub_group_id' => 'information',
            ));
        }
        $builder
            ->add('publishDate', 'oo_date_picker', array(
                'widget' => 'single_text',
                'label' => 'open_orchestra_backoffice.form.content.publish_date',
                'group_id' => 'property',
                'required' => false
            ))
            ->add('unpublishDate', 'oo_date_picker', array(
                'widget' => 'single_text',
                'label' => 'open_orchestra_backoffice.form.content.unpublish_date',
                'group_id' => 'property',
                'required' => false
            ));

        $builder->addEventSubscriber($this->statusableChoiceStatusSubscriber);
        $builder->addEventSubscriber($this->contentTypeSubscriber);
        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
        $this->eventDispatcher->dispatch(ContentFormEvents::CONTENT_FORM_CREATION, new ContentFormEvent($builder));
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
        return 'oo_content';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->contentClass,
            'is_blocked_edition' => false,
            'need_link_to_site_defintion' => false,
            'delete_button' => false,
            'new_button' => false,
                'group_enabled' => true,
                'group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.content.group.property',
                    ),
                    'data' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.content.group.data',
                    ),
                ),
                'sub_group_render' => array(
                    'information' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.content.sub_group.information',
                    ),
                    'publication' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.content.sub_group.publication',
                    ),
                    'data' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.content.sub_group.data',
                    ),
                ),
        ));
    }
}
