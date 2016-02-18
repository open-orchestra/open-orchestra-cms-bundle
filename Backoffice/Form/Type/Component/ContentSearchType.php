<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\Backoffice\Exception\NotAllowedClassNameException;
use OpenOrchestra\Backoffice\EventSubscriber\ContentSearchSubscriber;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;

/**
 * Class ContentSearchType
 */
class ContentSearchType extends AbstractType
{
    protected $contentRepository;
    protected $contextManager;
    protected $transformer;

    /**
     * @param ContentRepositoryInterface $contentRepository
     * @param CurrentSiteIdInterface     $contextManager
     * @param string                     $transformerClass
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository,
        CurrentSiteIdInterface $contextManager,
        $transformerClass
    ) {
        $this->contentRepository = $contentRepository;
        $this->contextManager = $contextManager;
        $this->transformerClass = $transformerClass;
        if (!is_string($this->transformerClass) || !is_subclass_of($this->transformerClass, 'OpenOrchestra\Transformer\ConditionFromBooleanToBddTransformerInterface')) {
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
        $builder->add('choiceType', 'oo_operator_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_search.choice_type',
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
        if ($options['content_selector']) {
            $transformerClass = $this->transformerClass;
            $transformer = new $transformerClass();
            $transformer->setField('keywords');
            $builder->addEventSubscriber(
                new ContentSearchSubscriber(
                    $this->contentRepository,
                    $this->contextManager,
                    $transformer,
                    $options['content_attr']
            ));
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'content_selector' => false,
                'content_attr' => array()
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
        return 'oo_content_search';
    }
}
