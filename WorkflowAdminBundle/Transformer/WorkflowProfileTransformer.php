<?php

namespace OpenOrchestra\WorkflowAdminBundle\Transformer;

use OpenOrchestra\ApiBundle\Context\CMSGroupContext;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;
use OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class WorkflowProfileTransformer
 */
class WorkflowProfileTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $multiLanguagesChoiceManager;
    protected $workflowProfileRepository;

    /**
     * @param string                               $facadeClass
     * @param AuthorizationCheckerInterface        $authorizationChecker
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param WorkflowProfileRepositoryInterface   $workflowProfileRepository
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        WorkflowProfileRepositoryInterface   $workflowProfileRepository
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->workflowProfileRepository = $workflowProfileRepository;
    }

    /**
     * @param WorkflowProfileInterface $workflowProfile
     * @param array                    $params
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($workflowProfile, array $params = array())
    {
        if (!$workflowProfile instanceof WorkflowProfileInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $workflowProfile->getId();
        $facade->label = $this->multiLanguagesChoiceManager->choose($workflowProfile->getLabels());
        $facade->description = $this->multiLanguagesChoiceManager->choose($workflowProfile->getDescriptions());
        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS)) {
            $canDelete = $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $workflowProfile);
            $facade->addRight('can_delete', $canDelete);
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array           $params
     *
     * @return WorkflowProfileInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, array $params = array())
    {
        if (null !== $facade->id) {
            return $this->workflowProfileRepository->find($facade->id);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'workflow_profile';
    }
}
