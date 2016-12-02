<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\StatusChangeNotGrantedHttpException;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Exception\StatusChangeNotGrantedException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class ContentTransformer
 */
class ContentTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $statusRepository;
    protected $contentTypeRepository;
    protected $eventDispatcher;

    /**
     * @param string                         $facadeClass
     * @param StatusRepositoryInterface      $statusRepository
     * @param ContentTypeRepositoryInterface $contentTypeRepository,
     * @param EventDispatcherInterface       $eventDispatcher
     * @param AuthorizationCheckerInterface  $authorizationChecker
     */
    public function __construct(
        $facadeClass,
        StatusRepositoryInterface $statusRepository,
        ContentTypeRepositoryInterface $contentTypeRepository,
        EventDispatcherInterface $eventDispatcher,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->statusRepository = $statusRepository;
        $this->contentTypeRepository = $contentTypeRepository;
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct($facadeClass, $authorizationChecker);
    }

    /**
     * @param ContentInterface $content
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($content)
    {
        if (!$content instanceof ContentInterface) {
            throw new TransformerParameterTypeException();
        }

        $contentType = $this->contentTypeRepository->findOneByContentTypeIdInLastVersion($content->getContentType());

        $facade = $this->newFacade();

        $facade->id = $content->getContentId();
        $facade->contentType = $content->getContentType();
        $facade->name = $content->getName();
        $facade->version = $content->getVersion();
        $facade->contentTypeVersion = $content->getContentTypeVersion();
        $facade->language = $content->getLanguage();
        $facade->status = $this->getTransformer('status')->transform($content->getStatus());
        $facade->statusLabel = $facade->status->label;
        $facade->createdAt = $content->getCreatedAt();
        $facade->updatedAt = $content->getUpdatedAt();
        $facade->createdBy = $content->getCreatedBy();
        $facade->updatedBy = $content->getUpdatedBy();
        $facade->deleted = $content->isDeleted();
        $facade->linkedToSite = $content->isLinkedToSite();

        foreach ($content->getAttributes() as $attribute) {
            $contentAttribute = $this->getTransformer('content_attribute')->transform($attribute);
            $facade->addAttribute($contentAttribute);
        }

        if ($this->authorizationChecker->isGranted(ContributionActionInterface::READ, $content->getId())) {
            if ($this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $content->getId())
                && !$content->getStatus()->isBlockedEdition()
            ) {
                $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_content_form', array(
                    'contentId' => $content->getContentId(),
                    'language' => $content->getLanguage(),
                    'version' => $content->getVersion(),
                )));
            }

            if ($this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, $content->getId())
                && $contentType->isDefiningVersionable()
            ) {
                $facade->addLink('_self_new_version', $this->generateRoute('open_orchestra_api_content_new_version', array(
                    'contentId' => $content->getContentId(),
                    'language' => $content->getLanguage(),
                    'version' => $content->getVersion(),
                )));
                $facade->addLink('_self_duplicate', $this->generateRoute('open_orchestra_api_content_duplicate', array(
                    'contentId' => $content->getContentId(),
                )));
            }

            if (
                $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $content->getId()) &&
                !$content->isUsed()
            ) {
                $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_content_delete', array(
                    'contentId' => $content->getId()
                )));
            }
        }
        if ($contentType->isDefiningVersionable()) {
            $facade->addLink('_self_version', $this->generateRoute('open_orchestra_api_content_list_version', array(
                'contentId' => $content->getContentId(),
                'language' => $content->getLanguage(),
            )));
        }

        $facade->addLink('_self', $this->generateRoute('open_orchestra_api_content_show_or_create', array(
            'contentId' => $content->getContentId(),
            'version' => $content->getVersion(),
            'language' => $content->getLanguage(),
        )));

        $facade->addLink('_self_without_parameters', $this->generateRoute('open_orchestra_api_content_show_or_create', array(
            'contentId' => $content->getContentId(),
        )));

        $facade->addLink('_language_list', $this->generateRoute('open_orchestra_api_parameter_languages_show'));

        $facade->addLink('_status_list', $this->generateRoute('open_orchestra_api_content_list_status', array(
            'contentMongoId' => $content->getId()
        )));

        $facade->addLink('_self_status_change', $this->generateRoute('open_orchestra_api_content_update', array(
            'contentMongoId' => $content->getId()
        )));

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param ContentInterface|null         $source
     *
     * @return mixed
     * @throws StatusChangeNotGrantedHttpException
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if ($source) {
            if ($facade->statusId) {
                $toStatus = $this->statusRepository->find($facade->statusId);
                if ($toStatus) {
                    $event = new StatusableEvent($source, $toStatus);
                    try {
                        $this->eventDispatcher->dispatch(StatusEvents::STATUS_CHANGE, $event);
                    } catch (StatusChangeNotGrantedException $e) {
                        throw new StatusChangeNotGrantedHttpException();
                    }
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
