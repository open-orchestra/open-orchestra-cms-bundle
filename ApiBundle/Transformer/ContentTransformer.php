<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\ContentFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelInterface\Event\StatusableEvent;
use PHPOrchestra\ModelInterface\StatusEvents;
use PHPOrchestra\ModelInterface\Model\ContentInterface;
use PHPOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ContentTransformer
 */
class ContentTransformer extends AbstractTransformer
{
    protected $statusRepository;
    protected $eventDispatcher;

    /**
     * @param StatusRepositoryInterface $statusRepository
     * @param EventDispatcherInterface  $eventDispatcher
     */
    public function __construct(StatusRepositoryInterface $statusRepository, $eventDispatcher)
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

        foreach ($mixed->getAttributes() as $attribute) {
            $facade->addAttribute($this->getTransformer('content_attribute')->transform($attribute));
        }

        $facade->addLink('_self_form', $this->generateRoute('php_orchestra_backoffice_content_form', array(
            'contentId' => $mixed->getContentId(),
            'language' => $mixed->getLanguage(),
            'version' => $mixed->getVersion(),
        )));

        $facade->addLink('_self_duplicate', $this->generateRoute('php_orchestra_api_content_duplicate', array(
            'contentId' => $mixed->getContentId(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_version', $this->generateRoute('php_orchestra_api_content_list_version', array(
            'contentId' => $mixed->getContentId(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_delete', $this->generateRoute('php_orchestra_api_content_delete', array(
            'contentId' => $mixed->getId()
        )));

        $facade->addLink('_self', $this->generateRoute('php_orchestra_api_content_show', array(
            'contentId' => $mixed->getContentId(),
            'version' => $mixed->getVersion(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_without_parameters', $this->generateRoute('php_orchestra_api_content_show', array(
            'contentId' => $mixed->getContentId(),
        )));

        $facade->addLink('_language_list', $this->generateRoute('php_orchestra_api_parameter_languages_show'));

        $facade->addLink('_status_list', $this->generateRoute('php_orchestra_api_list_status_content', array(
            'contentMongoId' => $mixed->getId()
        )));
        $facade->addLink('_self_status_change', $this->generateRoute('php_orchestra_api_content_update', array(
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
                $newStatus = $this->statusRepository->find($facade->statusId);
                if ($newStatus) {
                    $source->setStatus($newStatus);
                    $event = new StatusableEvent($source);
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
