<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SiteAliasType
 */
class SiteAliasType extends AbstractType
{
    protected $siteAliasClass;

    /**
     * @param string $siteAliasClass
     */
    public function __construct($siteAliasClass)
    {
        $this->siteAliasClass = $siteAliasClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domain', 'text', array(
                'label' => 'php_orchestra_backoffice.form.website.domain'
            ))
            ->add('defaultLanguage', 'orchestra_language', array(
                'label' => 'php_orchestra_backoffice.form.website.default_language'
            ))
            ->add('languages', 'orchestra_language', array(
                'label' => 'php_orchestra_backoffice.form.website.languages',
                'multiple' => true
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->siteAliasClass,
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'site_alias';
    }

}
