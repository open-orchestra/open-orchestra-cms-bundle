<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

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
        $data = $builder->getData();
        if (!array_key_exists('site', $data)) {
            $data['site'] = array();
        }

        $builder->add('label', 'text', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.label',
        ));
        $builder->add('site', 'oo_site_site_alias', array(
            'label' => false,
            'required' => true,
            'data' => $data['site'],
        ));
        $builder->add('contentSearch', 'oo_content_search', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.content',
            'search_engine' => true,
            'required' => false,
            'attr' => array('class' => 'form-to-patch'),
        ));
        $builder->add('query', 'text', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.query',
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
