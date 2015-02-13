<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\NodeFacade;
use PHPOrchestra\ModelInterface\Event\StatusableEvent;
use PHPOrchestra\ModelInterface\StatusEvents;
use PHPOrchestra\BaseBundle\Manager\EncryptionManager;
use PHPOrchestra\ModelInterface\Model\NodeInterface;
use PHPOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use PHPOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class NodeTransformer
 */
class NodeTransformer extends AbstractTransformer
{
    protected $encrypter;
    protected $siteRepository;
    protected $eventDispatcher;
    protected $statusRepository;

    /**
     * @param EncryptionManager         $encrypter
     * @param SiteRepositoryInterface   $siteRepository
     * @param StatusRepositoryInterface $statusRepository
     * @param EventDispatcherInterface  $eventDispatcher
     */
    public function __construct(
        EncryptionManager $encrypter,
        SiteRepositoryInterface $siteRepository,
        StatusRepositoryInterface $statusRepository,
        $eventDispatcher
    )
    {
        $this->encrypter = $encrypter;
        $this->siteRepository = $siteRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->statusRepository = $statusRepository;
    }

    /**
     * @param NodeInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new NodeFacade();

        foreach ($mixed->getAreas() as $area) {
            $facade->addArea($this->getTransformer('area')->transform($area, $mixed));
        }

        $facade->id = $mixed->getId();
        $facade->nodeId = $mixed->getNodeId();
        $facade->name = $mixed->getName();
        $facade->siteId = $mixed->getSiteId();
        $facade->deleted = $mixed->getDeleted();
        $facade->templateId = $mixed->getTemplateId();
        $facade->nodeType = $mixed->getNodeType();
        $facade->parentId = $mixed->getParentId();
        $facade->path = $mixed->getPath();
        $facade->routePattern = $mixed->getRoutePattern();
        $facade->language = $mixed->getLanguage();
        $facade->metaKeywords = $mixed->getMetaKeywords();
        $facade->metaDescription = $mixed->getMetaDescription();
        $facade->metaIndex = $mixed->getMetaIndex();
        $facade->metaFollow = $mixed->getMetaFollow();
        $facade->status = $this->getTransformer('status')->transform($mixed->getStatus());
        $facade->theme = $mixed->getTheme();
        $facade->version = $mixed->getVersion();
        $facade->createdBy = $mixed->getCreatedBy();
        $facade->updatedBy = $mixed->getUpdatedBy();
        $facade->createdAt = $mixed->getCreatedAt();
        $facade->updatedAt = $mixed->getUpdatedAt();

        $facade->addLink('_self_form', $this->generateRoute('php_orchestra_backoffice_node_form', array(
            'id' => $mixed->getId(),
        )));

        $facade->addLink('_self_duplicate', $this->generateRoute('php_orchestra_api_node_duplicate', array(
            'nodeId' => $mixed->getNodeId(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_version', $this->generateRoute('php_orchestra_api_node_list_version', array(
            'nodeId' => $mixed->getNodeId(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_delete', $this->generateRoute('php_orchestra_api_node_delete', array(
            'nodeId' => $mixed->getNodeId()
        )));

        $facade->addLink('_self_without_language', $this->generateRoute('php_orchestra_api_node_show', array(
            'nodeId' => $mixed->getNodeId()
        )));

        $facade->addLink('_self', $this->generateRoute('php_orchestra_api_node_show', array(
            'nodeId' => $mixed->getNodeId(),
            'version' => $mixed->getVersion(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_language_list', $this->generateRoute('php_orchestra_api_site_show', array(
            'siteId' => $mixed->getSiteId(),
        )));

        if ($site = $this->siteRepository->findOneBySiteId($mixed->getSiteId())) {
            $facade->addLink('_self_preview', 'http://' . $site->getMainAlias()->getDomain() . '/preview?token=' . $this->encrypter->encrypt($mixed->getId()));
        }

        $facade->addLink('_status_list', $this->generateRoute('php_orchestra_api_list_status_node', array(
            'nodeMongoId' => $mixed->getId()
        )));

        $facade->addLink('_self_status_change', $this->generateRoute('php_orchestra_api_node_update', array(
            'nodeMongoId' => $mixed->getId()
        )));

        $facade->addLink('_existing_block', $this->generateRoute('php_orchestra_backoffice_block_exsting', array(
            'language' => $mixed->getLanguage(),
        )));

        return $facade;
    }

    /**
     * @param NodeInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transformVersion($mixed)
    {
        $facade = new NodeFacade();

        $facade->id = $mixed->getId();
        $facade->nodeId = $mixed->getNodeId();
        $facade->name = $mixed->getName();
        $facade->version = $mixed->getVersion();
        $facade->createdBy = $mixed->getCreatedBy();
        $facade->updatedBy = $mixed->getUpdatedBy();
        $facade->createdAt = $mixed->getCreatedAt();
        $facade->updatedAt = $mixed->getUpdatedAt();
        $facade->status = $this->getTransformer('status')->transform($mixed->getStatus());

        $facade->addLink('_self', $this->generateRoute('php_orchestra_api_node_show', array(
            'nodeId' => $mixed->getNodeId(),
            'version' => $mixed->getVersion(),
            'language' => $mixed->getLanguage(),
        )));

        return $facade;
    }

    /**
     * @param NodeFacade|FacadeInterface $facade
     * @param NodeInterface              $source
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
        return 'node';
    }

}
