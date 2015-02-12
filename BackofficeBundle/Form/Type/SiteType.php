<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
            ->add('siteId', 'text', array(
                'label' => 'php_orchestra_backoffice.form.website.site_id'
            ))
            ->add('name', 'text', array(
                'label' => 'php_orchestra_backoffice.form.website.name'
            ))
            ->add('aliases', 'collection', array(
                'type' => 'site_alias',
                'label' => 'php_orchestra_backoffice.form.website.aliases',
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('php_orchestra_backoffice.form.field_option.add'),
                    'data-prototype-label-new' => $this->translator->trans('php_orchestra_backoffice.form.field_option.new'),
                    'data-prototype-label-remove' => $this->translator->trans('php_orchestra_backoffice.form.field_option.delete'),
                )
            ))
            ->add('blocks', 'orchestra_block', array(
                'multiple' => true,
                'label' => 'php_orchestra_backoffice.form.website.blocks',
                'required' => false
            ))
            ->add('theme', 'orchestra_theme', array(
                'label' => 'php_orchestra_backoffice.form.website.theme'
            ))
            ->add('sitemap_changefreq', 'orchestra_frequence_choice', array(
                'label' => 'php_orchestra_backoffice.form.website.changefreq.title'
            ))
            ->add('sitemap_priority', 'percent', array(
                'label' => 'php_orchestra_backoffice.form.node.priority',
                'type' => 'fractional',
                'precision' => 2
            ))
            ->add('metaKeywords', 'text', array(
                'label' => 'php_orchestra_backoffice.form.website.meta_keywords',
                'required' => false,
            ))
            ->add('metaDescription', 'text', array(
                'label' => 'php_orchestra_backoffice.form.website.meta_description',
                'required' => false,
            ))
            ->add('metaIndex', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.form.website.meta_index',
                'required' => false,
            ))
            ->add('metaFollow', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.form.website.meta_follow',
                'required' => false,
            ))
            ->add('robotsTxt', 'textarea', array(
                'label' => 'php_orchestra_backoffice.form.website.robots_txt',
                'required' => true
            ))
            ;

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
        return 'site';
    }
}
