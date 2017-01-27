<?php

namespace OpenOrchestra\ApiBundle\Transformer;

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

/**
 * Class ContentTypeTransformer
 */
class ContentTypeTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $multiLanguagesChoiceManager;
    protected $contentRepository;
    protected $contentTypeRepository;

    /**
     * @param string                               $facadeClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param ContentRepositoryInterface           $contentRepository
     * @param ContentTypeRepositoryInterface       $contentTypeRepository
     */
    public function __construct(
        $facadeClass,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        ContentRepositoryInterface $contentRepository,
        ContentTypeRepositoryInterface $contentTypeRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->contentRepository = $contentRepository;
        $this->contentTypeRepository = $contentTypeRepository;

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
        $facade->defaultListable = $contentType->getDefaultListable();

        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS)) {
            $facade->addRight('can_delete', $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $contentType) && 0 == $this->contentRepository->countByContentType($contentType->getContentTypeId()));
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
