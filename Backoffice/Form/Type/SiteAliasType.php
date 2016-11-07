<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;

/**
 * Class SiteAliasType
 */
class SiteAliasType extends AbstractType
{
    protected $siteAliasClass;
    protected $schemeChoices;

    /**
     * @param string $siteAliasClass
     */
    public function __construct(
        $siteAliasClass
    ) {
        $this->siteAliasClass = $siteAliasClass;
        $this->schemeChoices = array(
            SchemeableInterface::SCHEME_HTTP => 'open_orchestra_backoffice.scheme.http',
            SchemeableInterface::SCHEME_HTTPS => 'open_orchestra_backoffice.scheme.https'
        );
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('scheme', 'choice', array(
                'choices' => $this->schemeChoices,
                'label' => 'open_orchestra_backoffice.form.website.scheme',
                'group_rank' => 0,
                'sub_group' => 'open_orchestra_backoffice.form.alias.sub_group.property',
            ))
            ->add('domain', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.domain',
                'group_rank' => 0,
                'sub_group' => 'open_orchestra_backoffice.form.alias.sub_group.property',
            ))
            ->add('language', 'orchestra_language', array(
                'label' => 'open_orchestra_backoffice.form.website.language',
                'group_rank' => 0,
                'sub_group' => 'open_orchestra_backoffice.form.alias.sub_group.property',
            ))
            ->add('prefix', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.prefix',
                'required' => false,
                'group_rank' => 0,
                'sub_group' => 'open_orchestra_backoffice.form.alias.sub_group.property',
            ))
            ->add('main', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.website.main',
                'required' => false,
                'group_rank' => 0,
                'sub_group' => 'open_orchestra_backoffice.form.alias.sub_group.property',
            ))
            ->add('metaDescription', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_description',
                'required' => false,
                'group_rank' => 1,
                'sub_group' => 'open_orchestra_backoffice.form.alias.sub_group.meta',
            ))
            ->add('metaIndex', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_index',
                'required' => false,
                'group_rank' => 1,
                'sub_group' => 'open_orchestra_backoffice.form.alias.sub_group.meta',
            ))
            ->add('metaFollow', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_follow',
                'required' => false,
                'group_rank' => 1,
                'sub_group' => 'open_orchestra_backoffice.form.alias.sub_group.meta',
            ))
            ->add('googleMarker', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.google_marker',
                'required' => false,
                'group_rank' => 1,
                'sub_group' => 'open_orchestra_backoffice.form.alias.sub_group.google_marker',
            ))
            ->add('cnilCompliance', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.website.cnil_compliance',
                'required' => false,
                'group_rank' => 1,
                'sub_group' => 'open_orchestra_backoffice.form.alias.sub_group.google_marker',
            ))
            ->add('xtsd', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.xtsd',
                'required' => false,
                'group_rank' => 1,
                'sub_group' => 'open_orchestra_backoffice.form.alias.sub_group.xiti',
            ))
            ->add('xtside', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.xtside',
                'required' => false,
                'group_rank' => 1,
                'sub_group' => 'open_orchestra_backoffice.form.alias.sub_group.xiti',
            ))
            ->add('xtn2', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.xtn2',
                'required' => false,
                'group_rank' => 1,
                'sub_group' => 'open_orchestra_backoffice.form.alias.sub_group.xiti',
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->siteAliasClass,
                'group_enabled' => true,
                'group_label' => array(
                    'open_orchestra_backoffice.form.alias.group.information',
                    'open_orchestra_backoffice.form.alias.group.seo',
                )
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
        return 'oo_site_alias';
    }

}
