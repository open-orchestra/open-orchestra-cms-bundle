<?php

namespace OpenOrchestra\ApiBundle\Transformer;

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
     * @param ContentTypeInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ContentTypeFacade();

        $facade->id = $mixed->getId();
        $facade->contentTypeId = $mixed->getContentTypeId();
        $facade->name = $this->translationChoiceManager->choose($mixed->getNames());
        $facade->version = $mixed->getVersion();

        foreach ($mixed->getFields() as $field) {
            $facade->addField($this->getTransformer('field_type')->transform($field));
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_content_type_show',
            array('contentTypeId' => $mixed->getContentTypeId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_content_type_delete',
            array('contentTypeId' => $mixed->getContentTypeId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_content_type_form',
            array('contentTypeId' => $mixed->getContentTypeId())
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
