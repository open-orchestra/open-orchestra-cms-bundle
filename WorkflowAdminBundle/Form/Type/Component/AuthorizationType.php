<?php

namespace OpenOrchestra\WorkflowAdminBundle\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;

/**
 * Class AuthorizationType
 */
class AuthorizationType extends AbstractType
{
    protected $contentTypeRepository;
    protected $translationChoiceManager;
    protected $authorizationClass;

    /**
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param string                         $authorizationClass
     */
    public function __construct(ContentTypeRepositoryInterface $contentTypeRepository, $authorizationClass)
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->authorizationClass = $authorizationClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('referenceId', 'hidden');
        $builder->add('owner', 'checkbox', array(
            'label' => false,
            'required' => false
        ));
        $builder->add('workflowFunctions', 'oo_workflow_function_choice', array(
            'label' => false,
            'required' => false
        ));
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $contentTypeName = 'open_orchestra_backoffice.left_menu.editorial.nodes';
        $contentType = $this->contentTypeRepository->find($view->vars['value']->getReferenceId());
        if (null !== $contentType) {
            $contentTypeName = $contentType->getNames();
        }

        $view->vars['label'] = $contentTypeName;
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->authorizationClass,
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
        return 'oo_authorization';
    }

}
