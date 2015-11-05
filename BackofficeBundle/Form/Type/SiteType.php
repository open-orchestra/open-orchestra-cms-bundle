<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\WebSiteSubscriber;
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

    /**
     * @param string              $siteClass
     * @param TranslatorInterface $translator
     */
    public function __construct($siteClass, TranslatorInterface $translator)
    {
        $this->siteClass = $siteClass;
        $this->translator = $translator;
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
            ));
        $builder->add('siteId', 'text', array(
            'label' => 'open_orchestra_backoffice.form.website.site_id',
            'attr' => array('class' => 'generate-id-dest'),
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
            ))
            ->add('blocks', 'oo_block_choice', array(
                'multiple' => true,
                'label' => 'open_orchestra_backoffice.form.website.blocks',
                'required' => false
            ))
            ->add('theme', 'orchestra_theme', array(
                'label' => 'open_orchestra_backoffice.form.website.theme'
            ))
            ->add('sitemap_changefreq', 'orchestra_frequence_choice', array(
                'label' => 'open_orchestra_backoffice.form.website.changefreq.title',
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.website.changefreq.helper'),
            ))
            ->add('sitemap_priority', 'percent', array(
                'label' => 'open_orchestra_backoffice.form.node.priority.label',
                'type' => 'fractional',
                'precision' => 2,
                'attr' => array('help_text' => 'open_orchestra_backoffice.form.node.priority.helper'),
            ))
            ->add('metaKeywords', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_keywords',
                'required' => false,
            ))
            ->add('metaDescription', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_description',
                'required' => false,
            ))
            ->add('metaIndex', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_index',
                'required' => false,
            ))
            ->add('metaFollow', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_follow',
                'required' => false,
            ))
            ->add('robotsTxt', 'textarea', array(
                'label' => 'open_orchestra_backoffice.form.website.robots_txt',
                'required' => true,
            ))
            ;
        $builder->addEventSubscriber(new WebSiteSubscriber());
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
