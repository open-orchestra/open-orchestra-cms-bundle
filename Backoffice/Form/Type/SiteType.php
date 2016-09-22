<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\EventSubscriber\WebSiteNodeTemplateSubscriber;
use OpenOrchestra\Backoffice\EventSubscriber\WebSiteSubscriber;
use OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class SiteType
 */
class SiteType extends AbstractType
{
    protected $siteClass;
    protected $translator;
    protected $templateRepository;
    protected $frontLanguages;

    /**
     * @param string                      $siteClass
     * @param TranslatorInterface         $translator
     * @param TemplateRepositoryInterface $templateRepository
     * @param array                       $frontLanguages
     */
    public function __construct(
        $siteClass,
        TranslatorInterface $translator,
        TemplateRepositoryInterface $templateRepository,
        array $frontLanguages
    ){
        $this->siteClass = $siteClass;
        $this->translator = $translator;
        $this->templateRepository = $templateRepository;
        $this->frontLanguages = \array_keys($frontLanguages);
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
                'tabulation_rank' => 0,
            ));
        $builder->add('siteId', 'text', array(
            'label' => 'open_orchestra_backoffice.form.website.site_id',
            'attr' => array('class' => 'generate-id-dest'),
            'tabulation_rank' => 1,
        ));
        $builder
            ->add('aliases', 'collection', array(
                'type' => 'oo_site_alias',
                'label' => 'open_orchestra_backoffice.form.website.aliases',
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('open_orchestra_backoffice.form.field_option.add'),
                    'data-prototype-label-new' => $this->translator->trans('open_orchestra_backoffice.form.field_option.new'),
                    'data-prototype-label-remove' => $this->translator->trans('open_orchestra_backoffice.form.field_option.delete'),
                ),
                'options' => array( 'label' => false ),
                'tabulation_rank' => 0,
            ))
            ->add('blocks', 'oo_block_choice', array(
                'multiple' => true,
                'label' => 'open_orchestra_backoffice.form.website.blocks',
                'required' => false,
                'tabulation_rank' => 2,
            ))
            ->add('theme', 'oo_site_theme_choice', array(
                'label' => 'open_orchestra_backoffice.form.website.theme',
                'tabulation_rank' => 0,
            ))
            ->add('sitemap_changefreq', 'orchestra_frequence_choice', array(
                'label' => 'open_orchestra_backoffice.form.website.changefreq.title',
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.website.changefreq.helper'),
                'tabulation_rank' => 2,
            ))
            ->add('sitemap_priority', 'percent', array(
                'label' => 'open_orchestra_backoffice.form.node.priority.label',
                'type' => 'fractional',
                'precision' => 2,
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.node.priority.helper'),
                'tabulation_rank' => 1,
            ))
            ->add('metaKeywords', 'oo_multi_languages', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_keywords',
                'required' => false,
                'languages' => $this->frontLanguages,
                'tabulation_rank' => 0,
            ))
            ->add('metaDescriptions', 'oo_multi_languages', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_description',
                'required' => false,
                'languages' => $this->frontLanguages
            ))
            ->add('metaIndex', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_index',
                'required' => false,
                'tabulation_rank' => 1,
            ))
            ->add('metaFollow', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_follow',
                'required' => false,
                'tabulation_rank' => 0,
            ))
            ->add('robotsTxt', 'textarea', array(
                'label' => 'open_orchestra_backoffice.form.website.robots_txt',
                'required' => true,
                'tabulation_rank' => 2,
            ))
            ;
        $builder->addEventSubscriber(new WebSiteSubscriber());
        $builder->addEventSubscriber(new WebSiteNodeTemplateSubscriber($this->templateRepository));
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
                'tabulation_enabled' => true
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
