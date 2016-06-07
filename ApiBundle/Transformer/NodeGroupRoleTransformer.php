<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\Collector\RoleCollectorInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class NodeGroupRoleTransformer
 */
class NodeGroupRoleTransformer extends ModelGroupRoleTransformer
{
    protected $modelGroupRoleClass;
    protected $collector;
    protected $nodeRepository;
    protected $currentSiteManager;

    /**
     * @param string                  $facadeClass
     * @param string                  $modelGroupRoleClass
     * @param RoleCollectorInterface  $collector
     * @param NodeRepositoryInterface $nodeRepository
     * @param CurrentSiteIdInterface  $currentSiteManager
     */
    public function __construct(
        $facadeClass,
        $modelGroupRoleClass,
        RoleCollectorInterface $collector,
        NodeRepositoryInterface $nodeRepository,
        CurrentSiteIdInterface $currentSiteManager
    ) {
        parent::__construct($facadeClass, $modelGroupRoleClass, $collector);
        $this->nodeRepository = $nodeRepository;
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @param GroupInterface  $group
     * @param FacadeInterface $facade
     *
     * @return bool
     */
    protected function isParentAccessGranted(GroupInterface $group, FacadeInterface $facade)
    {
        $siteId = $group->getSite()->getSiteId();
        $node = $this->nodeRepository->findOneByNodeAndSite($facade->modelId, $siteId);
        $parentAccess = $group->getModelGroupRoleByTypeAndIdAndRole(
            NodeInterface::GROUP_ROLE_TYPE,
            $node->getParentId(),
            $facade->name
        );

        return $parentAccess->isGranted();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node_group_role';
    }
}
