<?php
namespace OpenOrchestra\WorkflowAdminBundle\Form\Type\Component;

use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\ModelInterface\Model\WorkflowFunctionInterface;

/**
 * Class WorkflowFunctionChoiceType
 */
class WorkflowFunctionChoiceType extends AbstractType
{
    protected $workflowFunctionClass;
    protected $multiLanguagesChoiceManager;

    /**
     * @param string                               $workflowFunctionClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     */
    public function __construct($workflowFunctionClass, MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager)
    {
        $this->workflowFunctionClass = $workflowFunctionClass;
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $multiLanguagesChoiceManage = $this->multiLanguagesChoiceManager;
        $resolver->setDefaults(
            array(
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'class' => $this->workflowFunctionClass,
                'choice_label' => function (WorkflowFunctionInterface $choice) use ($multiLanguagesChoiceManage) {
                    return $multiLanguagesChoiceManage->choose($choice->getNames());
                },
            )
        );
    }

    /**
     * Returns the name of the parent type.
     *
     * You can also return a type instance from this method, although doing so
     * is discouraged because it leads to a performance penalty. The support
     * for returning type instances may be dropped from future releases.
     *
     * @return string|null|FormTypeInterface The name of the parent type if any, null otherwise.
     */
    public function getParent()
    {
        return 'document';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_workflow_function_choice';
    }
}
