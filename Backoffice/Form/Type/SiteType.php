<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use OpenOrchestra\Backoffice\EventSubscriber\WebSiteNodeTemplateSubscriber;
use OpenOrchestra\Backoffice\EventSubscriber\WebSiteSubscriber;
use OpenOrchestra\Backoffice\Manager\TemplateManager;

/**
 * Class SiteType
 */
class SiteType extends AbstractType
{
    protected $siteClass;
    protected $translator;
    protected $templateManager;

    /**
     * @param string              $siteClass
     * @param TranslatorInterface $translator
     * @param TemplateManager     $templateManager
     */
    public function __construct(
        $siteClass,
        TranslatorInterface $translator,
        TemplateManager $templateManager
    ){
        $this->siteClass = $siteClass;
        $this->translator = $translator;
        $this->templateManager = $templateManager;
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
                'group_rank' => 0,
                'sub_group' => 'open_orchestra_backoffice.form.website.sub_group.property',
            ))
            ->add('aliases', 'collection', array(
                'type' => 'oo_site_alias',
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('open_orchestra_backoffice.form.field_option.add'),
                    'data-prototype-label-new' => $this->translator->trans('open_orchestra_backoffice.form.field_option.new'),
                    'data-prototype-label-remove' => $this->translator->trans('open_orchestra_backoffice.form.field_option.delete'),
                ),
                'options' => array( 'label' => false ),
                'group_rank' => 4,
            ))
            ->add('blocks', 'oo_block_choice', array(
                'multiple' => true,
                'expanded' => true,
                'label' => false,
                'required' => false,
                'group_rank' => 3,
                'sub_group' => 'open_orchestra_backoffice.form.website.sub_group.block',
            ))
            ->add('theme', 'oo_site_theme_choice', array(
                'label' => 'open_orchestra_backoffice.form.website.theme',
                'group_rank' => 1,
                'sub_group' => 'open_orchestra_backoffice.form.website.sub_group.property',
            ))
            ->add('metaAuthor', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.metaAuthor',
                'group_rank' => 2,
                'sub_group' => 'open_orchestra_backoffice.form.website.sub_group.meta',
            ))
            ->add('sitemap_changefreq', 'orchestra_frequence_choice', array(
                'label' => 'open_orchestra_backoffice.form.website.changefreq.title',
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.website.changefreq.helper'),
                'group_rank' => 2,
                'sub_group' => 'open_orchestra_backoffice.form.website.sub_group.sitemap',
            ))
            ->add('sitemap_priority', 'percent', array(
                'label' => 'open_orchestra_backoffice.form.node.priority.label',
                'type' => 'fractional',
                'precision' => 2,
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.node.priority.helper'),
                'group_rank' => 2,
                'sub_group' => 'open_orchestra_backoffice.form.website.sub_group.sitemap',
            ))
            ->add('robotsTxt', 'textarea', array(
                'label' => 'open_orchestra_backoffice.form.website.robots_txt',
                'required' => true,
                'group_rank' => 2,
                'sub_group' => 'open_orchestra_backoffice.form.website.sub_group.robot',
            ))
            ;
        $builder->addEventSubscriber(new WebSiteSubscriber());
        $builder->addEventSubscriber(new WebSiteNodeTemplateSubscriber($this->templateManager));
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
                'data_class' => $this->siteClass,
                'group_enabled' => true,
                'group_label' => array(
                    'open_orchestra_backoffice.form.website.group.information',
                    'open_orchestra_backoffice.form.website.group.template_set',
                    'open_orchestra_backoffice.form.website.group.seo',
                    'open_orchestra_backoffice.form.website.group.content',
                    'open_orchestra_backoffice.form.website.group.alias',
                )
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oo_site';
    }
}
