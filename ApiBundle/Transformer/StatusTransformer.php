<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\StatusFacade;
use PHPOrchestra\Backoffice\Manager\TranslationChoiceManager;
use PHPOrchestra\ModelBundle\Model\StatusInterface;
use Symfony\Component\Translation\Translator;
use PHPOrchestra\ModelBundle\Repository\RoleRepository;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
     * @param SecurityContextInterface $securityContext
     * @param RoleRepository $roleRepository
     * @param TranslationChoiceManager $translationChoiceManager
     * @param Translator               $translator
     */
    public function __construct(
        SecurityContextInterface $securityContext,
        RoleRepository $roleRepository,
        TranslationChoiceManager $translationChoiceManager,
        Translator $translator
    ) {
        $this->securityContext = $securityContext;
        $this->roleRepository = $roleRepository;
        $this->translationChoiceManager = $translationChoiceManager;
        $this->translator = $translator;
    }

    /**
     * @param StatusInterface $mixed
     * @param StatusInterface $currentStatus
     *
     * @return FacadeInterface|StatusFacade
     */
    public function transform($mixed, $currentStatus = null)
    {
        $facade = new StatusFacade();

        $facade->published = $mixed->isPublished();
        $facade->initial = $mixed->isInitial();
        $facade->label = $this->translationChoiceManager->choose($mixed->getLabels());
        $facade->displayColor = $this->translator->trans('php_orchestra_backoffice.form.status.color.' . $mixed->getDisplayColor());
        $facade->codeColor = $mixed->getDisplayColor();
        $facade->identifier = $mixed->getId();
        $facade->allowed = false;
        if ($currentStatus) {
            $role = $this->roleRepository->findOneByFromStatusAndToStatus($currentStatus, $mixed);
            if ($this->securityContext->isGranted($role->getName())) {
                $facade->allowed = true;
            }
        }

        $toRoles = array();
        foreach ($mixed->getToRoles() as $toRole) {
            $toRoles[] = $toRole->getName();
        }
        $facade->toRole = implode(',', $toRoles);
        $fromRoles = array();
        foreach ($mixed->getFromRoles() as $fromRole) {
            $fromRoles[] = $fromRole->getName();
        }
        $facade->fromRole = implode(',', $fromRoles);

        $facade->addLink('_self_delete', $this->generateRoute(
            'php_orchestra_api_status_delete',
            array('statusId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'php_orchestra_backoffice_status_form',
            array('statusId' => $mixed->getId())
        ));

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
