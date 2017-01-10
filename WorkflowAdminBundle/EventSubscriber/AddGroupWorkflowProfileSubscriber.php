<?php

namespace OpenOrchestra\WorkflowAdminBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use OpenOrchestra\GroupBundle\GroupFormEvents;
use OpenOrchestra\GroupBundle\Event\GroupFormEvent;
use OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\GroupBundle\Document\Perimeter;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;

/**
 * Class AddGroupWorkflowProfileSubscriber
 *
 */
class AddGroupWorkflowProfileSubscriber implements EventSubscriberInterface
{
    protected $workflowProfileRepository;
    protected $contentTypeRepository;
    protected $workflowProfileCollectionTransformer;
    protected $contextManager;
    protected $translator;

    /**
     * @param WorkflowProfileRepositoryInterface $workflowProfileRepository
     * @param ContentTypeRepositoryInterface     $contentTypeRepository
     * @param DataTransformerInterface           $workflowProfileCollectionTransformer
     * @param CurrentSiteIdInterface             $contextManager
     * @param TranslatorInterface                $translator
     */
    public function __construct(
        WorkflowProfileRepositoryInterface $workflowProfileRepository,
        ContentTypeRepositoryInterface $contentTypeRepository,
        DataTransformerInterface $workflowProfileCollectionTransformer,
        CurrentSiteIdInterface $contextManager,
        TranslatorInterface $translator
    ) {
        $this->workflowProfileRepository = $workflowProfileRepository;
        $this->contentTypeRepository = $contentTypeRepository;
        $this->workflowProfileCollectionTransformer = $workflowProfileCollectionTransformer;
        $this->contextManager = $contextManager;
        $this->translator = $translator;
    }

    /**
     * Triggered when a node status changes
     *
     * @param NodeEvent $event
     */
    public function addWorkflowProfile(GroupFormEvent $event)
    {
        $configuration = array();
        $workflowProfiles = $this->workflowProfileRepository->findAll();
        foreach ($workflowProfiles as $workflowProfile) {
            $configuration['default']['row'][] = $workflowProfile->getLabel($this->contextManager->getCurrentLocale());
        }

        $configuration['default']['column'][NodeInterface::ENTITY_TYPE] = $this->translator->trans('open_orchestra_workflow_admin.profile.page');
        $contentTypes = $this->contentTypeRepository->findAllNotDeletedInLastVersion();
        foreach ($contentTypes as $contentType) {
            $configuration['default']['column'][$contentType->getContentTypeId()] = $contentType->getName($this->contextManager->getCurrentLocale());
        }

        $builder = $event->getBuilder();
        $groupRender = $builder->getAttribute('group_render');
        $groupRender = array_merge($groupRender, array(
            'profile' => array(
                'rank' => '2',
                'label' => 'open_orchestra_workflow_admin.form.profile',
            )
        ));
        $builder->setAttribute('group_render', $groupRender);

        $subGroupRender = $builder->getAttribute('sub_group_render');
        $subGroupRender = array_merge($subGroupRender, array(
            'backoffice' => array(
                'rank' => '0',
                'label' => 'open_orchestra_workflow_admin.form.backoffice',
            )
        ));
        $builder->setAttribute('sub_group_render', $subGroupRender);
        $builder->add('workflow_profile_collections', 'oo_check_list_collection', array(
            'label' => false,
            'configuration' => $configuration,
            'group_id' => 'profile',
            'sub_group_id' => 'backoffice',
        ));
        $builder->get('workflow_profile_collections')->addModelTransformer($this->workflowProfileCollectionTransformer);
        $builder->addEventSubscriber($this);

    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        if (($group = $event->getData()) instanceof GroupInterface) {
            $workflowProfileCollections = $group->getWorkflowProfileCollections();
            $perimeter = new Perimeter();
            $perimeter->setType(ContentTypeInterface::ENTITY_TYPE);

            foreach ($workflowProfileCollections as $key => $workflowProfile) {
                if ($key != NodeInterface::ENTITY_TYPE && count($workflowProfile->getProfiles()) > 0) {
                    $perimeter->addItem($key);
                }
            }

            $group->addPerimeter($perimeter);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            GroupFormEvents::GROUP_FORM_CREATION => 'addWorkflowProfile',
            FormEvents::POST_SUBMIT => 'postSubmit',
        );
    }
}
