<?php

namespace OpenOrchestra\WorkflowAdminBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowFunctionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class WorkflowFunctionTransformer
 */
class WorkflowFunctionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $multiLanguagesChoiceManager;

    /**
     * @param string                               $facadeClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param AuthorizationCheckerInterface        $authorizationChecker
     */
    public function __construct(
        $facadeClass,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        AuthorizationCheckerInterface $authorizationChecker
    ){
        parent::__construct($facadeClass, $authorizationChecker);
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
    }

    /**
     * @param WorkflowFunctionInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = $this->newFacade();

        if (!is_null($mixed)) {
            $facade->id = $mixed->getId();
            $facade->name = $this->multiLanguagesChoiceManager->choose($mixed->getNames());

            $facade->addLink('_self', $this->generateRoute(
                'open_orchestra_api_workflow_function_show',
                array('workflowFunctionId' => $mixed->getId())
            ));

            if ($this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $mixed)) {
                $facade->addLink('_self_delete', $this->generateRoute(
                    'open_orchestra_api_workflow_function_delete',
                    array('workflowFunctionId' => $mixed->getId())
                ));
            }
            if ($this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $mixed->getId())) {
                $facade->addLink('_self_form', $this->generateRoute(
                    'open_orchestra_backoffice_workflow_function_form',
                    array('workflowFunctionId' => $mixed->getId())
                ));
            }
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'workflow_function';
    }
}
