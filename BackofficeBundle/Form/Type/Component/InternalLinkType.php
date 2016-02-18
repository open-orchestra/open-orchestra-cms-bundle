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
        $builder->add('label', 'text', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.label',
            'attr' => array(
                'class' => 'label-tinyMce',
            ),
        ));
        $builder->add('nodeId', 'oo_node_choice', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.node',
            'attr' => array(
                'class' => 'to-tinyMce',
                'data-key' => 'id'
            ),
        ));
        $builder->add('contentSearch', 'oo_content_search', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.content',
            'content_selector' => true,
            'required' => false,
            'content_attr' => array(
                'class' => 'to-tinyMce',
                'data-key' => 'content'
            ),
        ));
        $builder->add('site', 'oo_site_site_alias', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.site',
            'required' => false,
        ));
        $builder->add('query', 'text', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.query',
            'attr' => array(
                'class' => 'to-tinyMce',
                'data-key' => 'query'
            ),
            'required' => false,
        ));
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return "oo_internal_link";
    }

}
