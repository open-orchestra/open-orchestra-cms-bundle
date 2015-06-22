<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Context\GroupContext;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\StatusFacade;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class StatusTransformer
 */
class StatusTransformer extends AbstractTransformer
{
    protected $securityContext;
    protected $roleRepository;
    protected $translationChoiceManager;
    protected $translator;

    /**
     * @param AuthorizationCheckerInterface $securityContext
     * @param RoleRepositoryInterface       $roleRepository
     * @param TranslationChoiceManager      $translationChoiceManager
     * @param TranslatorInterface           $translator
     */
    public function __construct(
        AuthorizationCheckerInterface $securityContext,
        RoleRepositoryInterface $roleRepository,
        TranslationChoiceManager $translationChoiceManager,
        TranslatorInterface $translator
    ) {
        $this->securityContext = $securityContext;
        $this->roleRepository = $roleRepository;
        $this->translationChoiceManager = $translationChoiceManager;
        $this->translator = $translator;
    }

    /**
     * @param StatusInterface $status
     * @param StatusInterface $currentStatus
     *
     * @return FacadeInterface|StatusFacade
     */
    public function transform($status, $currentStatus = null)
    {
        $facade = new StatusFacade();

        $facade->published = $status->isPublished();
        $facade->initial = $status->isInitial();
        $facade->label = $this->translationChoiceManager->choose($status->getLabels());
        $facade->displayColor = $this->translator->trans('open_orchestra_backoffice.form.status.color.' . $status->getDisplayColor());
        $facade->codeColor = $status->getDisplayColor();
        $facade->id = $status->getId();
        $facade->allowed = false;
        if ($currentStatus) {
            $role = $this->roleRepository->findOneByFromStatusAndToStatus($currentStatus, $status);
            if ($this->securityContext->isGranted($role->getName())) {
                $facade->allowed = true;
            }
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

            $facade->addLink('_self_delete', $this->generateRoute(
                'open_orchestra_api_status_delete',
                array('statusId' => $status->getId())
            ));
            $facade->addLink('_self_form', $this->generateRoute(
                'open_orchestra_backoffice_status_form',
                array('statusId' => $status->getId())
            ));
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
