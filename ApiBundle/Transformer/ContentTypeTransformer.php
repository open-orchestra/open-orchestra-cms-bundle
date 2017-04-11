<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\BusinessRules\BusinessRulesManager;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\BusinessActionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\ApiBundle\Context\CMSGroupContext;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\Backoffice\Manager\ContentManager;

/**
 * Class ContentTypeTransformer
 */
class ContentTypeTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $multiLanguagesChoiceManager;
    protected $contentRepository;
    protected $contentTypeRepository;
    protected $contentManager;
    protected $businessRulesManager;

    /**
     * @param string                               $facadeClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param ContentRepositoryInterface           $contentRepository
     * @param ContentTypeRepositoryInterface       $contentTypeRepository
     * @param AuthorizationCheckerInterface        $authorizationChecker
     * @param BusinessRulesManager                 $businessRulesManager
     * @param ContentManager                       $contentManager
     */
    public function __construct(
        $facadeClass,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        ContentRepositoryInterface $contentRepository,
        ContentTypeRepositoryInterface $contentTypeRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        BusinessRulesManager $businessRulesManager,
        ContentManager $contentManager
    ) {
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->contentRepository = $contentRepository;
        $this->contentTypeRepository = $contentTypeRepository;
        $this->businessRulesManager = $businessRulesManager;
        $this->contentManager = $contentManager;
        parent::__construct($facadeClass, $authorizationChecker);
    }

    /**
     * @param ContentTypeInterface $contentType
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($contentType)
    {
        if (!$contentType instanceof ContentTypeInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $contentType->getId();
        $facade->contentTypeId = $contentType->getContentTypeId();
        $facade->name = $this->multiLanguagesChoiceManager->choose($contentType->getNames());
        $facade->version = $contentType->getVersion();
        $facade->linkedToSite = $contentType->isLinkedToSite();
        $facade->definingVersionable = $contentType->isDefiningVersionable();
        $facade->definingStatusable = $contentType->isDefiningStatusable();
        $facade->defaultListable = $contentType->getDefaultListable();

        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS)) {

            $facade->addRight('can_delete', $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $contentType) &&
                $this->businessRulesManager->isGranted(BusinessActionInterface::DELETE, $contentType));

            $facade->addRight('can_duplicate', $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, ContentTypeInterface::ENTITY_TYPE));

            $facade->addRight(
                'can_create',
                $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, ContentTypeInterface::ENTITY_TYPE)
            );

            $content = $this->contentManager->initializeNewContent(
                $contentType->getContentTypeId(),
                '',
                $contentType->isLinkedToSite() && $contentType->isAlwaysShared(),
                $contentType->isDefiningStatusable()
            );
            $facade->addRight(
                'can_create_content',
                $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, $content)
            );

        }

        if ($this->hasGroup(CMSGroupContext::FIELD_TYPES)) {
            foreach ($contentType->getFields() as $field) {
                $facade->addField($this->getTransformer('field_type')->transform($field));
            }
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null $source
     *
     * @return ContentTypeInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if (null !== $facade->contentTypeId) {
            return $this->contentTypeRepository->findOneByContentTypeIdInLastVersion($facade->contentTypeId);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_type';
    }
}
