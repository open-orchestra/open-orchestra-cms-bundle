<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\Transformer\LuceneToBddTransformerInterface;

/**
 * Class ContentSearchType
 */
class ContentSearchType extends AbstractType
{
    protected $transformer;

    /**
     * @param LuceneToBddTransformerInterface $transformer
     */
    public function __construct(LuceneToBddTransformerInterface $transformer)
    {
        $this->transformer = $transformer;
        $this->transformer->setField('keywords');
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addModelTransformer($this->transformer);

        $builder->add('contentType', 'oo_content_type_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_search.content_type',
            'required' => false
        ));
        $builder->add('keywords', 'oo_keywords_choice', array(
            'attr' => array(
                'class' => 'select2-lucene',
            ),
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
