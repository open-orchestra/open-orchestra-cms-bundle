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

        $facade = $this->newFacade();

        $facade->id = $content->getContentId();
        $facade->name = $content->getName();
        $facade->version = $content->getVersion();
        $facade->contentTypeVersion = $content->getContentTypeVersion();
        $facade->language = $content->getLanguage();
        $facade->status = $this->getTransformer('status')->transform($content->getStatus());
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

        $facade->addRight('can_edit', $this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, ContentInterface::ENTITY_TYPE));
        $facade->addRight('can_create', $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, ContentInterface::ENTITY_TYPE));
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
