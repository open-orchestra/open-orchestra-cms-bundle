<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\BusinessRules\BusinessRulesManager;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\BusinessActionInterface;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\ContentStrategy;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\StatusChangeNotGrantedHttpException;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
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
    protected $contentRepository;
    protected $contextManager;
    protected $businessRulesManager;

    /**
     * @param string                         $facadeClass
     * @param StatusRepositoryInterface      $statusRepository
     * @param ContentRepositoryInterface     $contentRepository,
     * @param AuthorizationCheckerInterface  $authorizationChecker
     * @param CurrentSiteIdInterface         $contextManager
     * @param BusinessRulesManager           $businessRulesManager
     */
    public function __construct(
        $facadeClass,
        StatusRepositoryInterface $statusRepository,
        ContentRepositoryInterface $contentRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        CurrentSiteIdInterface $contextManager,
        BusinessRulesManager $businessRulesManager
    ) {
        $this->statusRepository = $statusRepository;
        $this->contentRepository = $contentRepository;
        $this->contextManager = $contextManager;
        $this->businessRulesManager = $businessRulesManager;
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
        $facade->id = $content->getId();
        $facade->contentId = $content->getContentId();
        $facade->contentType = $content->getContentType();
        $facade->name = $content->getName();
        $facade->version = $content->getVersion();
        $facade->versionName = $content->getVersionName();
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
            $facade->addRight('can_delete', $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $content) &&
                $this->businessRulesManager->isGranted(BusinessActionInterface::DELETE, $content)
            );
            $facade->addRight('can_duplicate', $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, ContentInterface::ENTITY_TYPE));
            $facade->addRight('can_edit', $this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $content));
        }

        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS_DELETE_VERSION)) {
            $facade->addRight('can_delete_version', $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $content) &&
                $this->businessRulesManager->isGranted(ContentStrategy::DELETE_VERSION, $content)
            );
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
        if ($source instanceof ContentInterface &&
            null !== $facade->status &&
            null !== $facade->status->id &&
            $source->getStatus()->getId() !== $facade->status->id
        ) {
            $status = $this->statusRepository->find($facade->status->id);
            if ($status instanceof StatusInterface) {
                $source->setStatus($status);
            }
        }

        if (null !== $facade->id) {
            return $this->contentRepository->findById($facade->id);
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
