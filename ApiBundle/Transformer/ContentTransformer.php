<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\ContentFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelBundle\Model\ContentInterface;

/**
 * Class ContentTransformer
 */
class ContentTransformer extends AbstractTransformer
{
    /**
     * @param ContentInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ContentFacade();

        $facade->contentId = $mixed->getContentId();
        $facade->contentType = $mixed->getContentType();
        $facade->siteId = $mixed->getSiteId();
        $facade->name = $mixed->getName();
        $facade->version = $mixed->getVersion();
        $facade->contentTypeVersion = $mixed->getContentTypeVersion();
        $facade->language = $mixed->getLanguage();
        $facade->status = $this->getTransformer('status')->transform($mixed->getStatus());
        $facade->statusLabel = $facade->status->label;

        foreach ($mixed->getAttributes() as $attribute) {
            $facade->addAttribute($this->getTransformer('content_attribute')->transform($attribute));
        }

        $facade->addLink('_self', $this->generateRoute(
            'php_orchestra_api_content_show',
            array('contentId' => $mixed->getContentId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'php_orchestra_api_content_delete',
            array('contentId' => $mixed->getContentId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'php_orchestra_backoffice_content_form',
            array('contentId' => $mixed->getContentId())
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content';
    }
}
