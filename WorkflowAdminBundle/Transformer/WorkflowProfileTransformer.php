<?php

namespace OpenOrchestra\WorkflowAdminBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;
use OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface;

/**
 * Class WorkflowProfileTransformer
 */
class WorkflowProfileTransformer extends AbstractTransformer
{
    protected $multiLanguagesChoiceManager;
    protected $workflowProfileRepository;

    /**
     * @param string                               $facadeClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param WorkflowProfileRepositoryInterface   $workflowProfileRepository
     */
    public function __construct(
        $facadeClass,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        WorkflowProfileRepositoryInterface   $workflowProfileRepository
    ) {
        parent::__construct($facadeClass);
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->workflowProfileRepository = $workflowProfileRepository;
    }

    /**
     * @param WorkflowProfileInterface $workflowProfile
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($workflowProfile)
    {
        if (!$workflowProfile instanceof WorkflowProfileInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $workflowProfile->getId();
        $facade->label = $this->multiLanguagesChoiceManager->choose($workflowProfile->getLabels());
        $facade->description = $this->multiLanguagesChoiceManager->choose($workflowProfile->getDescriptions());


        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null $source
     *
     * @return WorkflowProfileInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
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
