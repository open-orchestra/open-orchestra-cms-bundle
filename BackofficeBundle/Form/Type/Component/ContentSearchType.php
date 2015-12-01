<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ContentSearchType
 */
class ContentSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contentType', 'oo_content_type_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_search.content_type',
            'required' => false
        ));
        $builder->add('choiceType', 'oo_operator_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_search.choice_type',
        ));
        $builder->add('keywords', 'oo_keywords_choice', array(
            'embedded' => false,
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.content_search.content_keyword',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_content_search';
    }
}
