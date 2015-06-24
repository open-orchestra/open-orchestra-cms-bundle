<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeHttpException;
use OpenOrchestra\ApiBundle\Facade\ContentTypeFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;

/**
 * Class ContentTypeTransformer
 */
class ContentTypeTransformer extends AbstractTransformer
{
    protected $translationChoiceManager;

    /**
     * @param TranslationChoiceManager $translationChoiceManager
     */
    public function __construct(TranslationChoiceManager $translationChoiceManager)
    {
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param ContentTypeInterface $contentType
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeHttpException
     */
    public function transform($contentType)
    {
        if (!$contentType instanceof ContentTypeInterface) {
            throw new TransformerParameterTypeHttpException();
        }

        $facade = new ContentTypeFacade();

        $facade->id = $contentType->getId();
        $facade->contentTypeId = $contentType->getContentTypeId();
        $facade->name = $this->translationChoiceManager->choose($contentType->getNames());
        $facade->version = $contentType->getVersion();
        $facade->linkedToSite = $contentType->isLinkedToSite();

        foreach ($contentType->getFields() as $field) {
            $facade->addField($this->getTransformer('field_type')->transform($field));
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_content_type_show',
            array('contentTypeId' => $contentType->getContentTypeId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_content_type_delete',
            array('contentTypeId' => $contentType->getContentTypeId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_content_type_form',
            array('contentTypeId' => $contentType->getContentTypeId())
        ));

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
