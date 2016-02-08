<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ContentSearchType
 */
class ContentSearchType extends AbstractType
{
    protected $transformer;

    /**
     * @param string $transformerClass
     */
    public function __construct($transformerClass)
    {
        $this->transformerClass = $transformerClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformerClass = $this->transformerClass;
        $transformer = new $transformerClass('keywords');
        $builder->addViewTransformer($transformer);

        $builder->add('contentType', 'oo_content_type_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_search.content_type',
            'required' => false
        ));
        $builder->add('keywords', 'oo_keywords_choice', array(
            'embedded' => false,
            'label' => 'open_orchestra_backoffice.form.content_search.content_keyword',
            'new_attr' => array(
                'class' => 'select-lucene',
            ),
            'required' => false,
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
