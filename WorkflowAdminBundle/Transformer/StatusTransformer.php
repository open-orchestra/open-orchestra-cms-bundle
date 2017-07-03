<?php

namespace OpenOrchestra\WorkflowAdminBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use OpenOrchestra\ApiBundle\Context\CMSGroupContext;
use OpenOrchestra\Backoffice\UsageFinder\StatusUsageFinder;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;

/**
 * Class StatusTransformer
 */
class StatusTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $multiLanguagesChoiceManager;
    protected $translator;
    protected $usageFinder;
    protected $statusRepository;

    /**
     * @param string                               $facadeClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param TranslatorInterface                  $translator
     * @param AuthorizationCheckerInterface        $authorizationChecker
     * @param StatusUsageFinder                    $usageFinder
     * @param StatusRepositoryInterface            $statusRepository
     */
    public function __construct(
        $facadeClass,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker,
        StatusUsageFinder $usageFinder,
        StatusRepositoryInterface $statusRepository
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->translator = $translator;
        $this->usageFinder = $usageFinder;
        $this->statusRepository = $statusRepository;
    }

    /**
     * @param StatusInterface $status
     * @param array|null      $params
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($status, array $params = null)
    {
        if (!$status instanceof StatusInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->publishedState = $status->isPublishedState();
        $facade->initialState = $status->isInitialState();
        $facade->blockedEdtion = $status->isBlockedEdition();
        $facade->autoPublishFromState = $status->isAutoPublishFromState();
        $facade->autoUnpublishToState = $status->isAutoUnpublishToState();
        $facade->translationState = $status->isTranslationState();
        $facade->name = $status->getName();
        $facade->label = $this->multiLanguagesChoiceManager->choose($status->getLabels());
        $facade->displayColor = $this->translator->trans('open_orchestra_workflow_admin.form.status.color.' . $status->getDisplayColor());
        $facade->codeColor = $status->getDisplayColor();
        $facade->id = $status->getId();

        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS)) {
            $canDelete = $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $status)
                && !$this->usageFinder->hasUsage($status)
                && !$status->isInitialState()
                && !$status->isPublishedState()
                && !$status->isTranslationState()
                && !$status->isAutoPublishFromState()
                && !$status->isAutoUnpublishToState();

            $facade->addRight('can_delete', $canDelete);
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array|null      $params
     *
     * @return UserInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, array $params = null)
    {
        if (null !== $facade->id) {
            return $this->statusRepository->find($facade->id);
        }

        return null;
    }

    /**
     * @return string
     */
    public function isCached()
    {
        return $this->hasGroup(CMSGroupContext::STATUS);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'status';
    }
}
