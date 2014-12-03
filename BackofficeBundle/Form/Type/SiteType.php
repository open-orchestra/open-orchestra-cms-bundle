<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BaseBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Router;
use PHPOrchestra\CMSBundle\Form\DataTransformer\SiteTypeTransformer;

/**
 * Class SiteType
 */
class SiteType extends AbstractType
{
    protected $siteClass;

    /**
     * @param string $siteClass
     */
    public function __construct($siteClass)
    {
        $this->siteClass = $siteClass;
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
            ->add('domain', 'text', array(
                'label' => 'php_orchestra_backoffice.form.website.domain'
            ))
            ->add('alias', 'text', array(
                'label' => 'php_orchestra_backoffice.form.website.alias'
            ))
            ->add('defaultLanguage', 'orchestra_language', array(
                'label' => 'php_orchestra_backoffice.form.website.default_language'
            ))
            ->add('languages', 'orchestra_language', array(
                'label' => 'php_orchestra_backoffice.form.website.languages',
                'multiple' => true
            ))
            ->add('blocks', 'orchestra_block', array(
                'multiple' => true,
                'label' => 'php_orchestra_backoffice.form.website.blocks'
            ))
            ->add('theme', 'document', array(
                'class'  => 'PHPOrchestra\ModelBundle\Document\Theme',
                'property' => 'name',
                'label' => 'php_orchestra_backoffice.form.website.theme'
            ));

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
