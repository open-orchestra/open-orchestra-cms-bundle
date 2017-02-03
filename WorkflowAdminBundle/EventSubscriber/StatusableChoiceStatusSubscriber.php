<?php

namespace OpenOrchestra\WorkflowAdminBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class StatusableChoiceStatusSubscriber
 */
class StatusableChoiceStatusSubscriber implements EventSubscriberInterface
{
    protected $statusRepository;
    protected $authorizationChecker;
    protected $options;

    /**
     * @param StatusRepositoryInterface     $statusRepository
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param array                         $options
     */
     public function __construct(
         StatusRepositoryInterface $statusRepository,
         AuthorizationCheckerInterface $authorizationChecker,
         array $options
    ) {
        $this->statusRepository = $statusRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->options = $options;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null !== $data->getId()) {
            $form->add('status', 'oo_status_choice', array_merge(array(
                'embedded' => true,
                'choices' => $this->getStatusChoices($data),
                'choice_attr' => function(StatusInterface $val, $key, $index) {
                    if ($val->isPublishedState()) {
                        return array('data-published-state' => true);
                    }

                    return array();
                }), $this->options)
            );
            $form->add('saveOldPublishedVersion', 'checkbox', array(
                'mapped' => false,
                'required' => false,
                'group_id' => isset($this->options['group_id']) ? $this->options['group_id'] : '',
                'sub_group_id' => isset($this->options['sub_group_id']) ? $this->options['sub_group_id'] : '',
                'label' => 'open_orchestra_backoffice.form.node.save_old_published_version',
            ));
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }

    /**
     * @param StatusableInterface $statusable
     *
     * @return array
     */
    protected function getStatusChoices(StatusableInterface $statusable)
    {
        $choices = array($statusable->getStatus());
        $availableStatus = $this->statusRepository->findAll();

        foreach ($availableStatus as $status) {
            if ($status->getId() != $statusable->getStatus()->getId()
                && $this->authorizationChecker->isGranted($status, $statusable)
            ) {
                $choices[] = $status;
            }
        }

        return $choices;
    }
}
