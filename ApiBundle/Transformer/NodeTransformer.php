<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\StatusChangeNotGrantedHttpException;
use OpenOrchestra\Backoffice\BusinessRules\BusinessRulesManager;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\NodeStrategy;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;
use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\BaseBundle\Manager\EncryptionManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\ApiBundle\Context\CMSGroupContext;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class NodeTransformer
 */
class NodeTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $encrypter;
    protected $siteRepository;
    protected $statusRepository;
    protected $eventDispatcher;
    protected $nodeRepository;
    protected $businessRulesManager;

    /**
     * @param string                        $facadeClass
     * @param EncryptionManager             $encrypter
     * @param SiteRepositoryInterface       $siteRepository
     * @param StatusRepositoryInterface     $statusRepository
     * @param EventDispatcherInterface      $eventDispatcher
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param NodeRepositoryInterface       $nodeRepository
     * @param BusinessRulesManager          $businessRulesManager
     */
    public function __construct(
        $facadeClass,
        EncryptionManager $encrypter,
        SiteRepositoryInterface $siteRepository,
        StatusRepositoryInterface $statusRepository,
        EventDispatcherInterface $eventDispatcher,
        AuthorizationCheckerInterface $authorizationChecker,
        NodeRepositoryInterface $nodeRepository,
        BusinessRulesManager $businessRulesManager
    ) {
        $this->encrypter = $encrypter;
        $this->siteRepository = $siteRepository;
        $this->statusRepository = $statusRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->nodeRepository = $nodeRepository;
        $this->businessRulesManager = $businessRulesManager;
        parent::__construct($facadeClass, $authorizationChecker);
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

        $facade = $this->newFacade();

        $facade = $this->addMainAttributes($facade, $node);
        $facade = $this->addAreas($facade, $node);
        $facade = $this->addStatus($facade, $node);
        $facade = $this->addPreviewLinks($facade, $node);

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param NodeInterface   $node
     *
     * @return FacadeInterface
     */
    protected function addMainAttributes(FacadeInterface $facade, NodeInterface $node)
    {
        if ($site = $this->siteRepository->findOneBySiteId($node->getSiteId())) {
            $facade->templateSet = $site->getTemplateSet();
        }

        $facade->id = $node->getId();
        $facade->nodeId = $node->getNodeId();
        $facade->name = $node->getName();
        $facade->siteId = $node->getSiteId();
        $facade->deleted = $node->isDeleted();
        $facade->template = $node->getTemplate();
        $facade->nodeType = $node->getNodeType();
        $facade->parentId = $node->getParentId();
        $facade->path = $node->getPath();
        $facade->routePattern = $node->getRoutePattern();
        $facade->language = $node->getLanguage();
        $facade->metaDescription = $node->getMetaDescription();
        $facade->metaIndex = $node->getMetaIndex();
        $facade->metaFollow = $node->getMetaFollow();
        $facade->version = $node->getVersion();
        $facade->versionName = $node->getVersionName();
        $facade->createdBy = $node->getCreatedBy();
        $facade->updatedBy = $node->getUpdatedBy();
        $facade->createdAt = $node->getCreatedAt();
        $facade->updatedAt = $node->getUpdatedAt();

        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS_DELETE_VERSION)) {
            $facade->addRight('can_delete', $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $node) && $this->businessRulesManager->isGranted(NodeStrategy::DELETE_VERSION, $node));
        }
        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS)) {
            $facade->addRight('can_read', $this->authorizationChecker->isGranted(ContributionActionInterface::READ, $node));
            $facade->addRight('can_edit', $this->businessRulesManager->isGranted(ContributionActionInterface::EDIT, $node) && $this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $node));
        }
        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param NodeInterface   $node
     *
     * @return FacadeInterface
     */
    protected function addAreas(FacadeInterface $facade, NodeInterface $node)
    {
        if ($this->hasGroup(CMSGroupContext::AREAS)) {
            foreach ($node->getAreas() as $key => $area) {
                $facade->setAreas($this->getTransformer('area')->transform($area), $key);
            }
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param NodeInterface   $node
     *
     * @return FacadeInterface
     */
    protected function addStatus(FacadeInterface $facade, NodeInterface $node)
    {
        if ($this->hasGroup(CMSGroupContext::STATUS)) {
            $facade->status = $this->getTransformer('status')->transform($node->getStatus());
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param NodeInterface   $node
     *
     * @return FacadeInterface
     */
    protected function addPreviewLinks(FacadeInterface $facade, NodeInterface $node)
    {
        if ($this->hasGroup(CMSGroupContext::PREVIEW) && $site = $this->siteRepository->findOneBySiteId($node->getSiteId())) {
            /** @var SiteAliasInterface $alias */
            $encryptedId = $this->encrypter->encrypt($node->getId());

            foreach ($site->getAliases() as $aliasId => $alias) {
                if ($alias->getLanguage() == $node->getLanguage()) {
                    $facade->addPreviewLink(
                        $this->getPreviewLink($node->getScheme(), $alias, $encryptedId, $aliasId)
                    );
                }
            }
        }

        return $facade;
    }

    /**
     * Get a preview link
     *
     * @param string             $scheme
     * @param SiteAliasInterface $alias
     * @param string             $encryptedId
     * @param int                $aliasId
     *
     * @return FacadeInterface
     */
    protected function getPreviewLink($scheme, $alias, $encryptedId, $aliasId)
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
            'aliasId' => $aliasId
        );

        $previewLink['link'] = $domain . $this->generateRoute($routeName, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);

        return $this->getTransformer('link')->transform($previewLink);
    }

    /**
     * @param FacadeInterface    $facade
     * @param NodeInterface|null $source
     *
     * @return mixed
     * @throws StatusChangeNotGrantedHttpException
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if ($source instanceof NodeInterface &&
            null !== $facade->status &&
            null !== $facade->status->id &&
            $source->getStatus()->getId() !== $facade->status->id
        ) {
            $status = $this->statusRepository->find($facade->status->id);
            if ($status instanceof StatusInterface) {
                $source->setStatus($status);
            }
        }

        if (null !== $facade->id) {
            return $this->nodeRepository->find($facade->id);
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
