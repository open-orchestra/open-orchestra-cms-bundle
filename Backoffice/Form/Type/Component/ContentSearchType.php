<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\Backoffice\EventSubscriber\ContentSearchSubscriber;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use OpenOrchestra\Backoffice\Validator\Constraints\BooleanCondition;

/**
 * Class ContentSearchType
 */
class ContentSearchType extends AbstractType
{
    protected $contentRepository;
    protected $contextManager;

    /**
     * @param ContentRepositoryInterface $contentRepository
     * @param CurrentSiteIdInterface     $contextManager
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository,
        CurrentSiteIdInterface $contextManager
    ) {
        $this->contentRepository = $contentRepository;
        $this->contextManager = $contextManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contentType', 'oo_content_type_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_search.content_type',
            'required' => !$options['refresh'] && $options['required']
        ));
        $builder->add('choiceType', 'oo_operator_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_search.choice_type',
            'required' => !$options['refresh'] && $options['required']
        ));
        $builder->add('keywords', 'oo_keywords_choice', array(
            'is_condition' => true,
            'label' => 'open_orchestra_backoffice.form.content_search.content_keyword',
            'constraints' => array(new BooleanCondition()),
            'name' => 'keywords',
            'new_attr' => array(
                'class' => 'select-boolean',
            ),
            'required' => !$options['refresh'] && $options['required'],
        ));

        if ($options['refresh']) {
            $builder->addEventSubscriber(
                new ContentSearchSubscriber(
                    $this->contentRepository,
                    $this->contextManager,
                    $options['attr'],
                    $options['required']
            ));
        }
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['refresh'] = $options['refresh'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'refresh' => false,
                'attr' => array()
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
