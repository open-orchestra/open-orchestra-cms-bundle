<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class NodeTransformer
 */
class NodeTransformer extends AbstractTransformer
{
    protected $encrypter;
    protected $siteRepository;
    protected $authorizationChecker;
    protected $eventDispatcher;
    protected $statusRepository;

    /**
     * @param EncryptionManager             $encrypter
     * @param SiteRepositoryInterface       $siteRepository
     * @param StatusRepositoryInterface     $statusRepository
     * @param EventDispatcherInterface      $eventDispatcher
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        EncryptionManager $encrypter,
        SiteRepositoryInterface $siteRepository,
        StatusRepositoryInterface $statusRepository,
        $eventDispatcher,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->encrypter = $encrypter;
        $this->siteRepository = $siteRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->statusRepository = $statusRepository;
        $this->authorizationChecker = $authorizationChecker;
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

        $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_node_form', array(
            'id' => $mixed->getId(),
        )));

        $facade->addLink('_self_duplicate', $this->generateRoute('open_orchestra_api_node_duplicate', array(
            'nodeId' => $mixed->getNodeId(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_version', $this->generateRoute('open_orchestra_api_node_list_version', array(
            'nodeId' => $mixed->getNodeId(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_node_delete', array(
            'nodeId' => $mixed->getNodeId()
        )));

        $facade->addLink('_self_without_language', $this->generateRoute('open_orchestra_api_node_show', array(
            'nodeId' => $mixed->getNodeId()
        )));

        $facade->addLink('_self', $this->generateRoute('open_orchestra_api_node_show', array(
            'nodeId' => $mixed->getNodeId(),
            'version' => $mixed->getVersion(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_language_list', $this->generateRoute('open_orchestra_api_site_show', array(
            'siteId' => $mixed->getSiteId(),
        )));

        if ($site = $this->siteRepository->findOneBySiteId($mixed->getSiteId())) {
            /** @var SiteAliasInterface $alias */
            $encryptedId = $this->encrypter->encrypt($mixed->getId());
            foreach ($site->getAliases() as $alias) {
                if ($alias->getLanguage() == $mixed->getLanguage()) {
                    $scheme = $mixed->getScheme();
                    if (is_null($scheme) || SchemeableInterface::SCHEME_DEFAULT == $scheme) {
                        $scheme = $alias->getScheme();
                    }
                    $previewLink = $scheme . '://' . $alias->getDomain() . '/preview?token=' . $encryptedId;
                    $facade->addPreviewLink($this->getTransformer('link')->transform(array('name' => $alias->getDomain(), 'link' => $previewLink)));
                }
            }
        }

        if (NodeInterface::TRANSVERSE_NODE_ID !== $mixed->getNodeId()) {
            $facade->addLink('_status_list', $this->generateRoute('open_orchestra_api_list_status_node', array(
                'nodeMongoId' => $mixed->getId()
            )));

            $facade->addLink('_self_status_change', $this->generateRoute('open_orchestra_api_node_update', array(
                'nodeMongoId' => $mixed->getId()
            )));
        }

        $facade->addLink('_block_list', $this->generateRoute('open_orchestra_api_block_list', array(
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

        $facade->addLink('_self', $this->generateRoute('open_orchestra_api_node_show', array(
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
                    $roles = array();
                    foreach ($newStatus->getToRoles() as $roleEntity) {
                        $roles[] = $roleEntity->getName();
                    }

                    if ($this->authorizationChecker->isGranted($roles)) {
                        $source->setStatus($newStatus);
                        $event = new StatusableEvent($source);
                        $this->eventDispatcher->dispatch(StatusEvents::STATUS_CHANGE, $event);
                    }
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
