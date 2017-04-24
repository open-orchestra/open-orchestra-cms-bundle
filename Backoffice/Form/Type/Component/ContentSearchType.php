<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\Backoffice\Validator\Constraints\BooleanConditionValidator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\Backoffice\EventSubscriber\ContentSearchSubscriber;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\Backoffice\Validator\Constraints\BooleanCondition;

/**
 * Class ContentSearchType
 */
class ContentSearchType extends AbstractType
{
    protected $contentRepository;
    protected $contextManager;

    /**
     * @param BooleanConditionValidator  $booleanConditionValidator,
     * @param ContentRepositoryInterface $contentRepository
     * @param CurrentSiteIdInterface     $contextManager
     */
    public function __construct(
        BooleanConditionValidator $booleanConditionValidator,
        ContentRepositoryInterface $contentRepository,
        CurrentSiteIdInterface $contextManager
    ) {
        $this->booleanConditionValidator = $booleanConditionValidator;
        $this->contentRepository = $contentRepository;
        $this->contextManager = $contextManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $newAttr = array();
        if (!is_null($options['authorize_new'])) {
            $newAttr['data-authorize-new'] = $options['authorize_new'];
        }

        $required = !$options['search_engine'] && $options['required'];

        $builder->add('contentType', 'oo_site_content_type_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_search.content_type',
            'required' => $required
        ));
        $builder->add('choiceType', 'oo_operator_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_search.choice_type',
            'required' => $required
        ));
        $builder->add('keywords', 'oo_keywords_choice', array(
            'is_condition' => true,
            'label' => 'open_orchestra_backoffice.form.content_search.content_keyword',
            'constraints' => array(new BooleanCondition()),
            'new_attr' => $newAttr,
            'required' => $required,
        ));

        if ($options['search_engine']) {
            $builder->addEventSubscriber(
                new ContentSearchSubscriber(
                    $this->booleanConditionValidator,
                    $this->contentRepository,
                    $this->contextManager,
                    $options['required']
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
                'search_engine' => false,
                'authorize_new' => null,
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
