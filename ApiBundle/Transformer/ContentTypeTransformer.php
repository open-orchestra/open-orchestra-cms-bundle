<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\ContentTypeFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelBundle\Model\ContentTypeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ContentTypeTransformer
 */
class ContentTypeTransformer extends AbstractTransformer
{
    /**
     * @param ContentTypeInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ContentTypeFacade();

        $facade->contentTypeId = $mixed->getContentTypeId();
        $facade->name = $mixed->getName();
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
