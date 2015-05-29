<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\ContentFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use OpenOrchestra\WorkflowFunction\Repository\WorkflowFunctionRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ContentTransformer
 */
class ContentTransformer extends AbstractTransformer
{
    protected $statusRepository;
    protected $roleRepository;
    protected $workflowFunctionRepository;
    protected $authorizationChecker;
    protected $eventDispatcher;

    /**
     * @param StatusRepositoryInterface           $statusRepository
     * @param RoleRepositoryInterface             $roleRepository
     * @param WorkflowFunctionRepositoryInterface $workflowFunctionRepository
     * @param AuthorizationCheckerInterface       $authorizationChecker
     * @param EventDispatcherInterface            $eventDispatcher
     */
    public function __construct(
        StatusRepositoryInterface $statusRepository,
        RoleRepositoryInterface $roleRepository,
        WorkflowFunctionRepositoryInterface $workflowFunctionRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        $eventDispatcher)
    {
        $this->statusRepository = $statusRepository;
        $this->roleRepository = $roleRepository;
        $this->workflowFunctionRepository = $workflowFunctionRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param ContentInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ContentFacade();

        $facade->id = $mixed->getContentId();
        $facade->contentType = $mixed->getContentType();
        $facade->name = $mixed->getName();
        $facade->version = $mixed->getVersion();
        $facade->contentTypeVersion = $mixed->getContentTypeVersion();
        $facade->language = $mixed->getLanguage();
        $facade->status = $this->getTransformer('status')->transform($mixed->getStatus());
        $facade->statusLabel = $facade->status->label;
        $facade->createdAt = $mixed->getCreatedAt();
        $facade->updatedAt = $mixed->getUpdatedAt();
        $facade->deleted = $mixed->getDeleted();

        foreach ($mixed->getAttributes() as $attribute) {
            $contentAttribute = $this->getTransformer('content_attribute')->transform($attribute);
            $facade->addAttribute($contentAttribute);
            $facade->addLinearizeAttribute($contentAttribute);
        }

        $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_content_form', array(
            'contentId' => $mixed->getContentId(),
            'language' => $mixed->getLanguage(),
            'version' => $mixed->getVersion(),
        )));

        $facade->addLink('_self_duplicate', $this->generateRoute('open_orchestra_api_content_duplicate', array(
            'contentId' => $mixed->getContentId(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_version', $this->generateRoute('open_orchestra_api_content_list_version', array(
            'contentId' => $mixed->getContentId(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_content_delete', array(
            'contentId' => $mixed->getId()
        )));

        $facade->addLink('_self', $this->generateRoute('open_orchestra_api_content_show_or_create', array(
            'contentId' => $mixed->getContentId(),
            'version' => $mixed->getVersion(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_without_parameters', $this->generateRoute('open_orchestra_api_content_show_or_create', array(
            'contentId' => $mixed->getContentId(),
        )));

        $facade->addLink('_language_list', $this->generateRoute('open_orchestra_api_parameter_languages_show'));

        $facade->addLink('_status_list', $this->generateRoute('open_orchestra_api_content_list_status', array(
            'contentMongoId' => $mixed->getId()
        )));
        $facade->addLink('_self_status_change', $this->generateRoute('open_orchestra_api_content_update', array(
            'contentMongoId' => $mixed->getId()
        )));

        return $facade;
    }

    /**
     * @param ContentFacade|FacadeInterface $facade
     * @param ContentInterface|null         $source
     *
     * @return mixed
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if ($source) {
            if ($facade->statusId) {
                $fromStatus = $source->getStatus();
                $toStatus = $this->statusRepository->find($facade->statusId);
                $granted = true;
                if ($fromStatus->getId() != $toStatus->getId()) {
                    $role = $this->roleRepository->findOneByFromStatusAndToStatus($fromStatus, $toStatus);
                    $workflowFunctions = $this->workflowFunctionRepository->findByRole($role);
                    $attributes = array();
                    foreach($workflowFunctions as $workflowFunction){
                        $attributes[] = $workflowFunction->getId();
                    }
                    $granted = $this->authorizationChecker->isGranted($attributes, $source);
                }

                if ($granted && $toStatus) {
                    $source->setStatus($toStatus);
                    $event = new StatusableEvent($source);
                    $this->eventDispatcher->dispatch(StatusEvents::STATUS_CHANGE, $event);
                }
            }
        }

        return $source;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content';
    }
}
