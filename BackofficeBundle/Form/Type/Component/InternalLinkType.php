<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class InternalLinkType
 */
class InternalLinkType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nodeId', 'oo_node_choice', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.node',
            'required' => false,
        ));
        $builder->add('contentSearch', 'oo_content_search', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.content',
            'content_selector' => true,
        ));
        $builder->add('siteId', 'oo_site_choice', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.site',
            'required' => false,
        ));
        $builder->add('query', 'text', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.query',
            'required' => false,
        ));
        ///site_alias: Site alias
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return "oo_internal_link";
    }

}
