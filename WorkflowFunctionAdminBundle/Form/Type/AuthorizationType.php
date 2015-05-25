<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\WorkflowFunction\Model\WorkflowRightInterface;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;

/**
 * Class AuthorizationType
 */
class AuthorizationType extends AbstractType
{
    protected $contentTypeRepository;

    protected $translationChoiceManager;

    protected $authorizationClass;

    /**
     * @param ContentTypeRepositoryInterface    $contentTypeRepository
     * @param TranslationChoiceManager          $translationChoiceManager
     * @param string                            $authorizationClass
     */
    public function __construct(ContentTypeRepositoryInterface $contentTypeRepository, TranslationChoiceManager $translationChoiceManager, $authorizationClass)
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->translationChoiceManager = $translationChoiceManager;
        $this->authorizationClass = $authorizationClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('referenceId', 'hidden');
        $builder->add('workflowFunctions', 'orchestra_workflow_function', array(
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
            $contentTypeName = $this->translationChoiceManager->choose($contentType->getNames());
        }
        $view->vars['label'] = $contentTypeName;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
        return 'authorization';
    }

}
