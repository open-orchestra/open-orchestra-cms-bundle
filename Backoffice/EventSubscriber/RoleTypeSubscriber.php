<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class RoleTypeSubscriber
 */
class RoleTypeSubscriber implements EventSubscriberInterface
{
    protected $roleRepository;
    protected $statusRepository;

    /**
     * @param RoleRepositoryInterface   $roleRepository
     * @param StatusRepositoryInterface $statusRepository
     * @param TranslatorInterface       $translator
     */
    public function __construct(RoleRepositoryInterface $roleRepository, StatusRepositoryInterface $statusRepository, TranslatorInterface $translator)
    {
        $this->roleRepository = $roleRepository;
        $this->statusRepository = $statusRepository;
        $this->translator = $translator;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $fromStatus = $this->statusRepository->find($data['fromStatus']);
        $toStatus = $this->statusRepository->find($data['toStatus']);
        $existingRole = $this->roleRepository->findOneByFromStatusAndToStatus($fromStatus, $toStatus);
        if (!is_null($existingRole)) {
            $message = $this->translator->trans("open_orchestra_backoffice.form.role.duplicate_statuses");
            $error = new FormError($message);
            $form->addError($error);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }
}
