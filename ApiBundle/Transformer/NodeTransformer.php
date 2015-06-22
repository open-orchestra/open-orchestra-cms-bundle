<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\NodeFacade;
use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;
use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\BaseBundle\Manager\EncryptionManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
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
     * @param EncryptionManager             $encrypter
     * @param SiteRepositoryInterface       $siteRepository
     * @param StatusRepositoryInterface     $statusRepository
     * @param EventDispatcherInterface      $eventDispatcher
     */
    public function __construct(
        EncryptionManager $encrypter,
        SiteRepositoryInterface $siteRepository,
        StatusRepositoryInterface $statusRepository,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->encrypter = $encrypter;
        $this->siteRepository = $siteRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->statusRepository = $statusRepository;
    }

    /**
     * @param NodeInterface $node
     *
     * @return FacadeInterface
     */
    public function transform($node)
    {
        $facade = new NodeFacade();

        foreach ($node->getAreas() as $area) {
            $facade->addArea($this->getTransformer('area')->transform($area, $node));
        }

        $facade->id = $node->getId();
        $facade->nodeId = $node->getNodeId();
        $facade->name = $node->getName();
        $facade->siteId = $node->getSiteId();
        $facade->deleted = $node->getDeleted();
        $facade->templateId = $node->getTemplateId();
        $facade->nodeType = $node->getNodeType();
        $facade->parentId = $node->getParentId();
        $facade->path = $node->getPath();
        $facade->routePattern = $node->getRoutePattern();
        $facade->language = $node->getLanguage();
        $facade->metaKeywords = $node->getMetaKeywords();
        $facade->metaDescription = $node->getMetaDescription();
        $facade->metaIndex = $node->getMetaIndex();
        $facade->metaFollow = $node->getMetaFollow();
        $facade->status = $this->getTransformer('status')->transform($node->getStatus());
        $facade->theme = $node->getTheme();
        $facade->version = $node->getVersion();
        $facade->createdBy = $node->getCreatedBy();
        $facade->updatedBy = $node->getUpdatedBy();
        $facade->createdAt = $node->getCreatedAt();
        $facade->updatedAt = $node->getUpdatedAt();

        $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_node_form', array(
            'id' => $node->getId(),
        )));

        $facade->addLink('_self_duplicate', $this->generateRoute('open_orchestra_api_node_duplicate', array(
            'nodeId' => $node->getNodeId(),
            'language' => $node->getLanguage(),
        )));

        $facade->addLink('_self_version', $this->generateRoute('open_orchestra_api_node_list_version', array(
            'nodeId' => $node->getNodeId(),
            'language' => $node->getLanguage(),
        )));

        $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_node_delete', array(
            'nodeId' => $node->getNodeId()
        )));

        $facade->addLink('_self_without_language', $this->generateRoute('open_orchestra_api_node_show_or_create', array(
            'nodeId' => $node->getNodeId()
        )));

        $facade->addLink('_self', $this->generateRoute('open_orchestra_api_node_show_or_create', array(
            'nodeId' => $node->getNodeId(),
            'version' => $node->getVersion(),
            'language' => $node->getLanguage(),
        )));

        $facade->addLink('_language_list', $this->generateRoute('open_orchestra_api_site_show', array(
            'siteId' => $node->getSiteId(),
        )));

        if ($site = $this->siteRepository->findOneBySiteId($node->getSiteId())) {
            /** @var SiteAliasInterface $alias */
            $encryptedId = $this->encrypter->encrypt($node->getId());
            foreach ($site->getAliases() as $alias) {
                if ($alias->getLanguage() == $node->getLanguage()) {
                    $scheme = $node->getScheme();
                    if (is_null($scheme) || SchemeableInterface::SCHEME_DEFAULT == $scheme) {
                        $scheme = $alias->getScheme();
                    }
                    $previewLink = $scheme . '://' . $alias->getDomain() . '/preview?token=' . $encryptedId;
                    $facade->addPreviewLink($this->getTransformer('link')->transform(array('name' => $alias->getDomain(), 'link' => $previewLink)));
                }
            }
        }

        if (NodeInterface::TRANSVERSE_NODE_ID !== $node->getNodeId()) {
            $facade->addLink('_status_list', $this->generateRoute('open_orchestra_api_node_list_status', array(
                'nodeMongoId' => $node->getId()
            )));

            $facade->addLink('_self_status_change', $this->generateRoute('open_orchestra_api_node_update', array(
                'nodeMongoId' => $node->getId()
            )));
        }

        $facade->addLink('_block_list', $this->generateRoute('open_orchestra_api_block_list', array(
            'language' => $node->getLanguage(),
        )));

        return $facade;
    }

    /**
     * @param NodeInterface $node
     *
     * @return FacadeInterface
     */
    public function transformVersion($node)
    {
        $facade = new NodeFacade();

        $facade->id = $node->getId();
        $facade->nodeId = $node->getNodeId();
        $facade->name = $node->getName();
        $facade->version = $node->getVersion();
        $facade->createdBy = $node->getCreatedBy();
        $facade->updatedBy = $node->getUpdatedBy();
        $facade->createdAt = $node->getCreatedAt();
        $facade->updatedAt = $node->getUpdatedAt();
        $facade->status = $this->getTransformer('status')->transform($node->getStatus());

        $facade->addLink('_self', $this->generateRoute('open_orchestra_api_node_show_or_create', array(
            'nodeId' => $node->getNodeId(),
            'version' => $node->getVersion(),
            'language' => $node->getLanguage(),
        )));

        return $facade;
    }

    /**
     * @param NodeFacade|FacadeInterface $facade
     * @param NodeInterface|null         $source
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
        return 'node';
    }

}
