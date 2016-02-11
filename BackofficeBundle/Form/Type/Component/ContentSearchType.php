<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\Backoffice\Exception\NotAllowedClassNameException;

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
        if (!is_string($this->transformerClass) || !is_subclass_of($this->transformerClass, 'OpenOrchestra\Transformer\ConditionFromBooleanToBddTransformer')) {
            throw new NotAllowedClassNameException();
        }
    }

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
        $builder->add('keywords', 'oo_keywords_choice', array(
            'embedded' => false,
            'transformerClass' => $this->transformerClass,
            'label' => 'open_orchestra_backoffice.form.content_search.content_keyword',
            'name' => 'keywords',
            'new_attr' => array(
                'class' => 'select-boolean',
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
