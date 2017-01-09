<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\ApiBundle\Context\CMSGroupContext;

/**
 * Class ContentTypeTransformer
 */
class ContentTypeTransformer extends AbstractTransformer
{
    protected $multiLanguagesChoiceManager;
    protected $contentRepository;

    /**
     * @param string                               $facadeClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param ContentRepositoryInterface           $contentRepository
     */
    public function __construct(
        $facadeClass,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        ContentRepositoryInterface $contentRepository
    ) {
        parent::__construct($facadeClass);
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->contentRepository = $contentRepository;
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

        if ($this->hasGroup(CMSGroupContext::FIELD_TYPES)) {
            foreach ($contentType->getFields() as $field) {
                $facade->addField($this->getTransformer('field_type')->transform($field));
            }
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_type';
    }
}
