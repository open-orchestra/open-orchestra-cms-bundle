<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\ContentTypeFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\Backoffice\Manager\TranslationChoiceManager;
use PHPOrchestra\ModelBundle\Model\ContentTypeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

        $facade->contentTypeId = $mixed->getContentTypeId();
        $facade->name = $this->translationChoiceManager->choose($mixed->getNames());
        $facade->version = $mixed->getVersion();
        $facade->status = $mixed->getStatus();
        foreach ($mixed->getFields() as $field) {
            $facade->addField($this->getTransformer('field_type')->transform($field));
        }

        $facade->addLink('_self', $this->getRouter()->generate(
            'php_orchestra_api_content_type_show',
            array('contentTypeId' => $mixed->getContentTypeId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
        $facade->addLink('_self_delete', $this->getRouter()->generate(
            'php_orchestra_api_content_type_delete',
            array('contentTypeId' => $mixed->getContentTypeId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
        $facade->addLink('_self_form', $this->getRouter()->generate(
            'php_orchestra_backoffice_content_type_form',
            array('contentTypeId' => $mixed->getContentTypeId()),
            UrlGeneratorInterface::ABSOLUTE_URL
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
