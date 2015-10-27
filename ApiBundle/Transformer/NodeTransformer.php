<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\StatusChangeNotGrantedHttpException;
use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Exception\StatusChangeNotGrantedException;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\BackofficeBundle\StrategyManager\AuthorizeEditionManager;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class NodeTransformer
 */
class NodeTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $encrypter;
    protected $siteRepository;
    protected $eventDispatcher;
    protected $statusRepository;
    protected $authorizeEdition;

    /**
     * @param EncryptionManager             $encrypter
     * @param SiteRepositoryInterface       $siteRepository
     * @param StatusRepositoryInterface     $statusRepository
     * @param EventDispatcherInterface      $eventDispatcher
     * @param AuthorizeEditionManager       $authorizeEdition
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        EncryptionManager $encrypter,
        SiteRepositoryInterface $siteRepository,
        StatusRepositoryInterface $statusRepository,
        EventDispatcherInterface $eventDispatcher,
        AuthorizeEditionManager $authorizeEdition,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->encrypter = $encrypter;
        $this->siteRepository = $siteRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->statusRepository = $statusRepository;
        $this->authorizeEdition = $authorizeEdition;
        parent::__construct($authorizationChecker);
    }

    /**
     * @param NodeInterface $node
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($node)
    {
        if (!$node instanceof NodeInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = new NodeFacade();

        foreach ($node->getAreas() as $area) {
            $facade->addArea($this->getTransformer('area')->transform($area, $node));
        }

        $facade->id = $node->getId();
        $nodeId = $node->getNodeId();
        $facade->nodeId = $nodeId;
        $facade->name = $node->getName();
        $facade->siteId = $node->getSiteId();
        $facade->deleted = $node->isDeleted();
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
        $facade->editable = $this->authorizeEdition->isEditable($node);

        $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_node_form', array(
            'id' => $node->getId(),
        )));

        if ($this->authorizationChecker->isGranted(TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE)) {
            $facade->addLink('_self_duplicate', $this->generateRoute('open_orchestra_api_node_duplicate', array(
                'nodeId' => $nodeId,
                'language' => $node->getLanguage(),
                'version' => $node->getVersion(),
            )));
        }

        $facade->addLink('_self_version', $this->generateRoute('open_orchestra_api_node_list_version', array(
            'nodeId' => $nodeId,
            'language' => $node->getLanguage(),
        )));

        if (!$node->getStatus()->isPublished() &&
            NodeInterface::TYPE_ERROR !== $node->getNodeType() &&
            $this->authorizationChecker->isGranted(TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE)
        ) {
            $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_node_delete', array(
                'nodeId' => $nodeId
            )));
        }

        $facade->addLink('_self_without_language', $this->generateRoute('open_orchestra_api_node_show_or_create', array(
            'nodeId' => $nodeId
        )));

        $facade->addLink('_self', $this->generateRoute('open_orchestra_api_node_show_or_create', array(
            'nodeId' => $nodeId,
            'version' => $node->getVersion(),
            'language' => $node->getLanguage(),
        )));

        $facade->addLink('_language_list', $this->generateRoute('open_orchestra_api_site_show', array(
            'siteId' => $node->getSiteId(),
        )));

        if ($site = $this->siteRepository->findOneBySiteId($node->getSiteId())) {
            /** @var SiteAliasInterface $alias */
            $encryptedId = $this->encrypter->encrypt($node->getId());
            foreach ($site->getAliases() as $aliasId => $alias) {
                if ($alias->getLanguage() == $node->getLanguage()) {
                    $facade->addPreviewLink(
                        $this->getPreviewLink($node->getScheme(), $alias, $encryptedId, $aliasId, $nodeId)
                    );
                }
            }
        }

        if (NodeInterface::TRANSVERSE_NODE_ID !== $nodeId) {
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
     * Get a preview link
     * 
     * @param string             $scheme
     * @param SiteAliasInterface $alias
     * @param string             $encryptedId
     * @param int                $aliasId
     * @param string             $nodeId
     *
     * @return FacadeInterface
     */
    protected function getPreviewLink($scheme, $alias, $encryptedId, $aliasId, $nodeId)
    {
        $previewLink = array(
            'name' => $alias->getDomain(),
            'link' => ''
        );

        if (is_null($scheme) || SchemeableInterface::SCHEME_DEFAULT == $scheme) {
            $scheme = $alias->getScheme();
        }
        $domain = $scheme . '://' . $alias->getDomain();
        $routeName = 'open_orchestra_base_node_preview';
        $parameters = array(
            'token' => $encryptedId,
            'aliasId' => $aliasId,
            'nodeId' => $nodeId
        );

        $previewLink['link'] = $domain . $this->generateRoute($routeName, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);

        return $this->getTransformer('link')->transform($previewLink);
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
     * @throws StatusChangeNotGrantedHttpException
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if ($source) {
            if ($facade->statusId) {
                $toStatus = $this->statusRepository->find($facade->statusId);
                if ($toStatus) {
                    $event = new StatusableEvent($source, $toStatus);
                    try {
                        $this->eventDispatcher->dispatch(StatusEvents::STATUS_CHANGE, $event);
                    } catch (StatusChangeNotGrantedException $e) {
                        throw new StatusChangeNotGrantedHttpException();
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
