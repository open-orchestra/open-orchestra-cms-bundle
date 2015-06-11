<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\ContentFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ContentTransformer
 */
class ContentTransformer extends AbstractTransformer
{
    protected $statusRepository;
    protected $eventDispatcher;

    /**
     * @param StatusRepositoryInterface           $statusRepository
     * @param EventDispatcherInterface            $eventDispatcher
     */
    public function __construct(
        StatusRepositoryInterface $statusRepository,
        EventDispatcherInterface $eventDispatcher)
    {
        $this->statusRepository = $statusRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param ContentInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ContentFacade();

        $facade->id = $mixed->getContentId();
        $facade->contentType = $mixed->getContentType();
        $facade->name = $mixed->getName();
        $facade->version = $mixed->getVersion();
        $facade->contentTypeVersion = $mixed->getContentTypeVersion();
        $facade->language = $mixed->getLanguage();
        $facade->status = $this->getTransformer('status')->transform($mixed->getStatus());
        $facade->statusLabel = $facade->status->label;
        $facade->createdAt = $mixed->getCreatedAt();
        $facade->updatedAt = $mixed->getUpdatedAt();
        $facade->deleted = $mixed->getDeleted();
        $facade->siteLinked = $mixed->isSiteLinked();

        foreach ($mixed->getAttributes() as $attribute) {
            $contentAttribute = $this->getTransformer('content_attribute')->transform($attribute);
            $facade->addAttribute($contentAttribute);
            $facade->addLinearizeAttribute($contentAttribute);
        }

        $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_content_form', array(
            'contentId' => $mixed->getContentId(),
            'language' => $mixed->getLanguage(),
            'version' => $mixed->getVersion(),
        )));

        $facade->addLink('_self_duplicate', $this->generateRoute('open_orchestra_api_content_duplicate', array(
            'contentId' => $mixed->getContentId(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_version', $this->generateRoute('open_orchestra_api_content_list_version', array(
            'contentId' => $mixed->getContentId(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_content_delete', array(
            'contentId' => $mixed->getId()
        )));

        $facade->addLink('_self', $this->generateRoute('open_orchestra_api_content_show_or_create', array(
            'contentId' => $mixed->getContentId(),
            'version' => $mixed->getVersion(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_without_parameters', $this->generateRoute('open_orchestra_api_content_show_or_create', array(
            'contentId' => $mixed->getContentId(),
        )));

        $facade->addLink('_language_list', $this->generateRoute('open_orchestra_api_parameter_languages_show'));

        $facade->addLink('_status_list', $this->generateRoute('open_orchestra_api_content_list_status', array(
            'contentMongoId' => $mixed->getId()
        )));
        $facade->addLink('_self_status_change', $this->generateRoute('open_orchestra_api_content_update', array(
            'contentMongoId' => $mixed->getId()
        )));

        return $facade;
    }

    /**
     * @param ContentFacade|FacadeInterface $facade
     * @param ContentInterface|null         $source
     *
     * @return mixed
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if ($source) {
            if ($facade->statusId) {
                $toStatus = $this->statusRepository->find($facade->statusId);
                if ($toStatus) {
                    $event = new StatusableEvent($source, $toStatus);
                    $this->eventDispatcher->dispatch(StatusEvents::STATUS_CHANGE, $event);
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
