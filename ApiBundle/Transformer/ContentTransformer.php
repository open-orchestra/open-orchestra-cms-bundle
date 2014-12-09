<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\ContentFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelBundle\Model\ContentInterface;
use PHPOrchestra\ModelBundle\Repository\StatusRepository;

/**
 * Class ContentTransformer
 */
class ContentTransformer extends AbstractTransformer
{
    protected $statusRepository;

    public function __construct(StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    /**
     * @param ContentInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ContentFacade();

        $facade->id = $mixed->getId();
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
            array('contentId' => $mixed->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'php_orchestra_api_content_delete',
            array('contentId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'php_orchestra_backoffice_content_form',
            array('contentId' => $mixed->getId())
        ));
        $facade->addLink('_status_list', $this->generateRoute('php_orchestra_api_list_status_content', array(
            'contentId' => $mixed->getId()
        )));
        $facade->addLink('_self_status_change', $this->generateRoute('php_orchestra_api_content_update', array(
            'contentId' => $mixed->getId()
        )));

        $facade->addLink('_language_list', $this->generateRoute('php_orchestra_api_site_show', array(
            'siteId' => $mixed->getSiteId(),
        )));

        $facade->addLink('_status_list', $this->generateRoute('php_orchestra_api_list_status_content', array(
            'contentMongoId' => $mixed->getId()
        )));

        $facade->addLink('_self_status_change', $this->generateRoute('php_orchestra_api_content_update', array(
            'contentMongoId' => $mixed->getId()
        )));

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param mixed|null      $source
     *
     * @return mixed
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if ($source) {
            if ($facade->statusId) {
                $newStatus = $this->statusRepository->find($facade->statusId);
                if ($newStatus) {
                    $source->setStatus($newStatus);
                }
            }
        }

        return $source;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content';
    }
}
