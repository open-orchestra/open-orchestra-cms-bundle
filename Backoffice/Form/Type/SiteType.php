<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\Event\SiteFormEvent;
use OpenOrchestra\Backoffice\SiteFormEvents;
use OpenOrchestra\Backoffice\Validator\Constraints\UnremovableLanguageCondition;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use OpenOrchestra\Backoffice\EventSubscriber\WebSiteNodeTemplateSubscriber;
use OpenOrchestra\Backoffice\Manager\TemplateManager;

/**
 * Class SiteType
 */
class SiteType extends AbstractType
{
    protected $siteClass;
    protected $translator;
    protected $templateManager;
    protected $webSiteSubscriber;
    protected $eventDispatcher;

    /**
     * @param string                   $siteClass
     * @param TranslatorInterface      $translator
     * @param TemplateManager          $templateManager
     * @param EventSubscriberInterface $webSiteSubscriber
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        $siteClass,
        TranslatorInterface $translator,
        TemplateManager $templateManager,
        EventSubscriberInterface $webSiteSubscriber,
        EventDispatcherInterface $eventDispatcher
    ){
        $this->siteClass = $siteClass;
        $this->translator = $translator;
        $this->templateManager = $templateManager;
        $this->webSiteSubscriber = $webSiteSubscriber;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.name',
                'attr' => array('class' => 'generate-id-source'),
                'group_id' => 'information',
                'sub_group_id' => 'property',
            ))
            ->add('aliases', 'collection', array(
                'type' => 'oo_site_alias',
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('open_orchestra_backoffice.form.website.add_alias'),
                    'data-prototype-label-remove' => $this->translator->trans('open_orchestra_backoffice.form.delete'),
                ),
                'constraints' => array(new UnremovableLanguageCondition($builder->getData())),
                'options' => array( 'label' => false ),
                'group_id' => 'alias',
            ))
            ->add('blocks', 'oo_block_choice', array(
                'multiple' => true,
                'expanded' => true,
                'label' => false,
                'required' => false,
                'group_id' => 'content',
                'sub_group_id' => 'block',
            ))
            ->add('contentTypes', 'oo_content_type_choice', array(
                'multiple' => true,
                'expanded' => true,
                'label' => false,
                'required' => false,
                'group_id' => 'content',
                'sub_group_id' => 'content_type',
            ))
            ->add('metaAuthor', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.metaAuthor',
                'group_id' => 'seo',
                'sub_group_id' => 'meta',
                'required' => false
            ))
            ->add('sitemap_changefreq', 'orchestra_frequence_choice', array(
                'label' => 'open_orchestra_backoffice.form.website.changefreq.title',
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.website.changefreq.helper'),
                'group_id' => 'seo',
                'sub_group_id' => 'sitemap',
            ))
            ->add('sitemap_priority', 'percent', array(
                'label' => 'open_orchestra_backoffice.form.node.priority.label',
                'type' => 'fractional',
                'precision' => 2,
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.node.priority.helper'),
                'group_id' => 'seo',
                'sub_group_id' => 'sitemap',
            ))
            ->add('robotsTxt', 'textarea', array(
                'label' => 'open_orchestra_backoffice.form.website.robots_txt',
                'required' => true,
                'group_id' => 'seo',
                'sub_group_id' => 'robot',
            ))
            ;
        $builder->addEventSubscriber($this->webSiteSubscriber);
        $builder->addEventSubscriber(new WebSiteNodeTemplateSubscriber($this->templateManager));
        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
        $this->eventDispatcher->dispatch(SiteFormEvents::SITE_FORM_CREATION, new SiteFormEvent($builder));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->siteClass,
                'delete_button' => false,
                'new_button' => false,
                'group_enabled' => true,
                'group_render' => array(
                    'information' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.website.group.information',
                    ),
                    'template_set' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.website.group.template_set',
                    ),
                    'seo' => array(
                        'rank' => 2,
                        'label' => 'open_orchestra_backoffice.form.website.group.seo',
                    ),
                    'content' => array(
                        'rank' => 3,
                        'label' => 'open_orchestra_backoffice.form.website.group.content',
                    ),
                    'alias' => array(
                        'rank' => 4,
                        'label' => 'open_orchestra_backoffice.form.website.group.alias',
                    ),
                ),
                'sub_group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.website.sub_group.property',
                    ),
                    'block' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.website.sub_group.block',
                    ),
                    'content_type' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.website.sub_group.content_type',
                    ),
                    'meta' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.website.sub_group.meta',
                    ),
                    'sitemap' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.website.sub_group.sitemap',
                    ),
                    'robot' => array(
                        'rank' => 2,
                        'label' => 'open_orchestra_backoffice.form.website.sub_group.robot',
                    ),
                    'alias' => array(
                        'rank' => 2,
                        'label' => 'open_orchestra_backoffice.form.website.sub_group.robot',
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
        return 'oo_site';
    }
}
