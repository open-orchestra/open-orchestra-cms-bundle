<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\StatusChangeNotGrantedHttpException;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ApiBundle\Context\CMSGroupContext;

/**
 * Class ContentTransformer
 */
class ContentTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $statusRepository;
    protected $contentTypeRepository;
    protected $contentRepository;
    protected $contextManager;

    /**
     * @param string                         $facadeClass
     * @param StatusRepositoryInterface      $statusRepository
     * @param ContentTypeRepositoryInterface $contentTypeRepository,
     * @param ContentRepositoryInterface     $contentRepository,
     * @param AuthorizationCheckerInterface  $authorizationChecker
     * @param CurrentSiteIdInterface         $contextManager
     */
    public function __construct(
        $facadeClass,
        StatusRepositoryInterface $statusRepository,
        ContentTypeRepositoryInterface $contentTypeRepository,
        ContentRepositoryInterface $contentRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        CurrentSiteIdInterface $contextManager
    )
    {
        $this->statusRepository = $statusRepository;
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contentRepository = $contentRepository;
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

        $facade->id = $content->getContentId();
        $facade->name = $content->getName();
        $facade->version = $content->getVersion();
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
        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS)) {
            $facade->addRight('can_duplicate', $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, ContentInterface::ENTITY_TYPE) && !is_null($contentType) && $contentType->isDefiningVersionable());
            $currentlyPublishedContents = $this->contentRepository->findAllCurrentlyPublishedByContentId($content->getContentId());
            $isUsed = false;
            foreach ($currentlyPublishedContents as $currentlyPublishedContent) {
                $isUsed = $isUsed || $currentlyPublishedContent->isUsed();
            }
            $facade->addRight('can_delete', $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $content) && !$isUsed);
        }

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
        if (null !== $facade->id) {
            return $this->contentRepository->findOneByContentId($facade->id);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content';
    }
}
