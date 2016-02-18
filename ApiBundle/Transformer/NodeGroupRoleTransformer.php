<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\Collector\RoleCollectorInterface;
use OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
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
    protected function isParentGranted(GroupInterface $group, FacadeInterface $facade)
    {
        $siteId = $this->currentSiteManager->getCurrentSiteId();
        $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
        $node = $this->nodeRepository->findInLastVersion($facade->document, $language, $siteId);
        $parentAccess = $group->getModelRoleByTypeAndIdAndRole(ModelGroupRoleInterface::TYPE_NODE, $node->getParentId(), $facade->name);
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
