<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\RoleNotFoundHttpException;
use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Collector\RoleCollectorInterface;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class NodeGroupRoleTransformer
 */
class NodeGroupRoleTransformer extends AbstractTransformer
{
    protected $nodeRoleGroupClass;
    protected $collector;
    protected $nodeRepository;
    protected $currentSiteManager;

    /**
     * @param string                  $facadeClass
     * @param string                  $nodeRoleGroupClass
     * @param RoleCollectorInterface  $collector
     * @param NodeRepositoryInterface $nodeRepository
     * @param CurrentSiteIdInterface  $currentSiteManager
     */
    public function __construct(
        $facadeClass,
        $nodeRoleGroupClass,
        RoleCollectorInterface $collector,
        NodeRepositoryInterface $nodeRepository,
        CurrentSiteIdInterface $currentSiteManager
    ) {
        parent::__construct($facadeClass);
        $this->nodeRoleGroupClass = $nodeRoleGroupClass;
        $this->collector = $collector;
        $this->nodeRepository = $nodeRepository;
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @param NodeGroupRoleInterface $nodeGroupRole
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($nodeGroupRole)
    {
        if (!$nodeGroupRole instanceof NodeGroupRoleInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->node = $nodeGroupRole->getNodeId();
        $facade->name = $nodeGroupRole->getRole();
        $facade->accessType = $nodeGroupRole->getAccessType();

        return $facade;
    }

    /**
     * @param FacadeInterface     $nodeRoleFacade
     * @param GroupInterface|null $group
     *
     * @throws RoleNotFoundHttpException
     * @throws TransformerParameterTypeException
     *
     * @return null|NodeGroupRoleInterface
     */
    public function reverseTransform(FacadeInterface $nodeRoleFacade, $group = null)
    {
        if (!$group instanceof GroupInterface) {
            throw new TransformerParameterTypeException();
        }

        $source = $group->getNodeRoleByNodeAndRole($nodeRoleFacade->node, $nodeRoleFacade->name);
        if (!$source instanceof NodeGroupRoleInterface) {
            $source = new $this->nodeRoleGroupClass();
        }

        if (!$this->collector->hasRole($nodeRoleFacade->name)) {
            throw new RoleNotFoundHttpException();
        }

        $source->setNodeId($nodeRoleFacade->node);
        $source->setRole($nodeRoleFacade->name);
        $source->setAccessType($nodeRoleFacade->accessType);

        if (NodeGroupRoleInterface::ACCESS_INHERIT === $nodeRoleFacade->accessType) {
            $siteId = $this->currentSiteManager->getCurrentSiteId();
            $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
            $node = $this->nodeRepository->findInLastVersion($nodeRoleFacade->node, $language, $siteId);
            $parentAccess = $group->getNodeRoleByNodeAndRole($node->getParentId(), $nodeRoleFacade->name);
            $source->setGranted($parentAccess->isGranted());
        } else {
            $isGranted = (NodeGroupRoleInterface::ACCESS_GRANTED === $nodeRoleFacade->accessType) ? true : false;
            $source->setGranted($isGranted);
        }

        return $source;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node_group_role';
    }
}
