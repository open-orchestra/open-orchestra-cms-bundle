<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Context\GroupContext;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use OpenOrchestra\BackofficeBundle\StrategyManager\authorizeStatusChangeManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Class StatusTransformer
 */
class StatusTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $authorizeStatusChangeManager;
    protected $roleRepository;
    protected $translationChoiceManager;
    protected $translator;

    /**
     * @param string                        $facadeClass
     * @param AuthorizeStatusChangeManager  $authorizeStatusChangeManager
     * @param RoleRepositoryInterface       $roleRepository
     * @param TranslationChoiceManager      $translationChoiceManager
     * @param TranslatorInterface           $translator
     */
    public function __construct(
        $facadeClass,
        AuthorizeStatusChangeManager $authorizeStatusChangeManager,
        RoleRepositoryInterface $roleRepository,
        TranslationChoiceManager $translationChoiceManager,
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->authorizeStatusChangeManager = $authorizeStatusChangeManager;
        $this->roleRepository = $roleRepository;
        $this->translationChoiceManager = $translationChoiceManager;
        $this->translator = $translator;
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

        $facade->published = $status->isPublished();
        $facade->initial = $status->isInitial();
        $facade->label = $this->translationChoiceManager->choose($status->getLabels());
        $facade->displayColor = $this->translator->trans('open_orchestra_backoffice.form.status.color.' . $status->getDisplayColor());
        $facade->codeColor = $status->getDisplayColor();
        $facade->id = $status->getId();
        $facade->allowed = false;
        if ($document) {
            $facade->allowed = $this->authorizeStatusChangeManager->isGranted($document, $status);
        }

        if (!$this->hasGroup(GroupContext::G_HIDE_ROLES)) {
            $toRoles = array();
            foreach ($status->getToRoles() as $toRole) {
                $toRoles[] = $toRole->getName();
            }
            $facade->toRole = implode(',', $toRoles);
            $fromRoles = array();
            foreach ($status->getFromRoles() as $fromRole) {
                $fromRoles[] = $fromRole->getName();
            }
            $facade->fromRole = implode(',', $fromRoles);

            if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_STATUS, $status)) {
                $facade->addLink('_self_delete', $this->generateRoute(
                    'open_orchestra_api_status_delete',
                    array('statusId' => $status->getId())
                ));
            }

            if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_STATUS)) {
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
