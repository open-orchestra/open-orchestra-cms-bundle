<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\StatusChangeNotGrantedHttpException;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Exception\StatusChangeNotGrantedException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;

/**
 * Class ContentTransformer
 */
class ContentTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $statusRepository;
    protected $contentTypeRepository;
    protected $contentRepository;
    protected $eventDispatcher;
    protected $contextManager;

    /**
     * @param string                         $facadeClass
     * @param StatusRepositoryInterface      $statusRepository
     * @param ContentTypeRepositoryInterface $contentTypeRepository,
     * @param ContentRepositoryInterface     $contentRepository,
     * @param EventDispatcherInterface       $eventDispatcher
     * @param AuthorizationCheckerInterface  $authorizationChecker
     * @param CurrentSiteIdInterface         $contextManager
     */
    public function __construct(
        $facadeClass,
        StatusRepositoryInterface $statusRepository,
        ContentTypeRepositoryInterface $contentTypeRepository,
        ContentRepositoryInterface $contentRepository,
        EventDispatcherInterface $eventDispatcher,
        AuthorizationCheckerInterface $authorizationChecker,
        CurrentSiteIdInterface $contextManager
    )
    {
        $this->statusRepository = $statusRepository;
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contentRepository = $contentRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->contextManager = $contextManager;
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

        $facade->id = $content->getId();
        $facade->name = $content->getName();
        $facade->version = $content->getVersion();
        $facade->contentTypeVersion = $content->getContentTypeVersion();
        $facade->language = $content->getLanguage();
        $facade->status = $this->getTransformer('status')->transform($content->getStatus());
        $facade->statusLabel = $content->getStatus()->getLabel($this->contextManager->getCurrentLocale());
        $facade->createdAt = $content->getCreatedAt();
        $facade->updatedAt = $content->getUpdatedAt();
        $facade->createdBy = $content->getCreatedBy();
        $facade->updatedBy = $content->getUpdatedBy();
        $facade->deleted = $content->isDeleted();
        $facade->linkedToSite = $content->isLinkedToSite();
        $facade->used = $content->isUsed();

        foreach ($content->getAttributes() as $attribute) {
            $contentAttribute = $this->getTransformer('content_attribute')->transform($attribute);
            $facade->addAttribute($contentAttribute);
        }

        $facade->addRight('can_edit', $this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, ContentInterface::ENTITY_TYPE) && !$content->isUsed());
        $facade->addRight('can_create', $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, ContentInterface::ENTITY_TYPE));
        $facade->addRight('can_duplicate', $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, ContentInterface::ENTITY_TYPE) && !is_null($contentType) && $contentType->isDefiningVersionable());
        $facade->addRight('can_delete', $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, ContentInterface::ENTITY_TYPE));

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
        } else {
            if (null !== $facade->id) {
                return $this->contentRepository->find($facade->id);
            }

            return null;

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
