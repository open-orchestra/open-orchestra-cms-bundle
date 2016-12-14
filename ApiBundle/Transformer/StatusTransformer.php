<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ApiBundle\Context\CMSGroupContext;
use OpenOrchestra\Backoffice\UsageFinder\StatusUsageFinder;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class StatusTransformer
 */
class StatusTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $multiLanguagesChoiceManager;
    protected $translator;
    protected $usageFinder;

    /**
     * @param string                               $facadeClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param TranslatorInterface                  $translator
     * @param AuthorizationCheckerInterface        $authorizationChecker
     * @param StatusUsageFinder                    $usageFinder
     */
    public function __construct(
        $facadeClass,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker,
        StatusUsageFinder $usageFinder
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->translator = $translator;
        $this->usageFinder = $usageFinder;
    }

    /**
     * @param StatusInterface          $status
     * @param StatusableInterface|null $document
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($status, $document = null)
    {
        if (!$status instanceof StatusInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->publishedState = $status->isPublishedState();
        $facade->initialState = $status->isInitialState();
        $facade->autoPublishFromState = $status->isAutoPublishFromState();
        $facade->autoUnpublishToState = $status->isAutoUnpublishToState();
        $facade->translationState = $status->isTranslationState();
        $facade->name = $status->getName();
        $facade->label = $this->multiLanguagesChoiceManager->choose($status->getLabels());
        $facade->displayColor = $this->translator->trans('open_orchestra_backoffice.form.status.color.' . $status->getDisplayColor());
        $facade->codeColor = $status->getDisplayColor();
        $facade->id = $status->getId();
        $facade->allowed = false;
        if ($document) {
            $facade->allowed = $this->authorizationChecker->isGranted($status, $document);
        }

        if ($this->hasGroup(CMSGroupContext::STATUS_LINKS)) {
            if ($this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $status)
                && !$this->usageFinder->hasUsage($status)
            ) {
                $facade->addLink('_self_delete', $this->generateRoute(
                    'open_orchestra_api_status_delete',
                    array('statusId' => $status->getId())
                ));
            }

            if ($this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $status)) {
                $facade->addLink('_self_form', $this->generateRoute(
                    'open_orchestra_backoffice_status_form',
                    array('statusId' => $status->getId())
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
        return 'status';
    }
}
