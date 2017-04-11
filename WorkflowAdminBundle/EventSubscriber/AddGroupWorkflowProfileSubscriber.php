<?php

namespace OpenOrchestra\WorkflowAdminBundle\EventSubscriber;

use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use OpenOrchestra\GroupBundle\GroupFormEvents;
use OpenOrchestra\GroupBundle\Event\GroupFormEvent;
use OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class AddGroupWorkflowProfileSubscriber
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
     * @param SiteRepositoryInterface            $siteRepository
     * @param DataTransformerInterface           $workflowProfileCollectionTransformer
     * @param CurrentSiteIdInterface             $contextManager
     * @param TranslatorInterface                $translator
     */
    public function __construct(
        WorkflowProfileRepositoryInterface $workflowProfileRepository,
        ContentTypeRepositoryInterface $contentTypeRepository,
        SiteRepositoryInterface $siteRepository,
        DataTransformerInterface $workflowProfileCollectionTransformer,
        CurrentSiteIdInterface $contextManager,
        TranslatorInterface $translator
    ) {
        $this->workflowProfileRepository = $workflowProfileRepository;
        $this->contentTypeRepository = $contentTypeRepository;
        $this->siteRepository = $siteRepository;
        $this->workflowProfileCollectionTransformer = $workflowProfileCollectionTransformer;
        $this->contextManager = $contextManager;
        $this->translator = $translator;
    }

    /**
     * add workflowProfile choice to group form
     *
     * @param GroupFormEvent $event
     */
    public function addWorkflowProfile(GroupFormEvent $event)
    {
        $builder = $event->getBuilder();
        $group = $builder->getData();
        $configuration = array();
        $workflowProfiles = $this->workflowProfileRepository->findAll();
        foreach ($workflowProfiles as $workflowProfile) {
            $configuration['default']['row'][] = $workflowProfile->getLabel($this->contextManager->getCurrentLocale());
        }

        $configuration['default']['column'][NodeInterface::ENTITY_TYPE] = $this->translator->trans('open_orchestra_workflow_admin.profile.page');
        if ($group instanceof GroupInterface) {
            $site = $group->getSite();
            if (!empty($site->getContentTypes())) {
                $contentTypes = $this->contentTypeRepository->findAllNotDeletedInLastVersion($site->getContentTypes());
                foreach ($contentTypes as $contentType) {
                    $configuration['default']['column'][$contentType->getContentTypeId()] = $contentType->getName($this->contextManager->getCurrentLocale());
                }
            }
        }
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
            'required' => false
        ));
        $builder->get('workflow_profile_collections')->addModelTransformer($this->workflowProfileCollectionTransformer);
   }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            GroupFormEvents::GROUP_FORM_CREATION => 'addWorkflowProfile',
        );
    }
}
