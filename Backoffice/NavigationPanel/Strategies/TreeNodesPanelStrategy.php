<?php

namespace OpenOrchestra\Backoffice\NavigationPanel\Strategies;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;

/**
 * Class TreeNodesPanel
 */
class TreeNodesPanelStrategy extends AbstractNavigationStrategy
{
    const ROLE_ACCESS_TREE_NODE = 'ROLE_ACCESS_TREE_NODE';
    const ROLE_ACCESS_CREATE_NODE = 'ROLE_ACCESS_CREATE_NODE';
    const ROLE_ACCESS_UPDATE_NODE = 'ROLE_ACCESS_UPDATE_NODE';
    const ROLE_ACCESS_DELETE_NODE = 'ROLE_ACCESS_DELETE_NODE';
    const ROLE_ACCESS_MOVE_TREE = 'ROLE_ACCESS_MOVE_TREE';
    const ROLE_ACCESS_ERROR_NODE = 'ROLE_ACCESS_ERROR_NODE';
    const ROLE_ACCESS_CREATE_ERROR_NODE = 'ROLE_ACCESS_CREATE_ERROR_NODE';
    const ROLE_ACCESS_UPDATE_ERROR_NODE = 'ROLE_ACCESS_UPDATE_ERROR_NODE';

    /**
     * @var NodeRepositoryInterface
     */
    protected $nodeRepository;

    /**
     * @var CurrentSiteIdInterface
     */
    protected $currentSiteManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param CurrentSiteIdInterface  $currentSiteManager
     * @param string                  $parent
     * @param int                     $weight
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, CurrentSiteIdInterface $currentSiteManager, $parent, $weight)
    {
        parent::__construct('nodes', $weight, $parent);
        $this->nodeRepository = $nodeRepository;
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @return string
     */
    public function show()
    {
        $siteId = $this->currentSiteManager->getCurrentSiteId();
        $nodes = $this->nodeRepository->findLastVersionByType($siteId);

        return $this->render(
            'OpenOrchestraBackofficeBundle:BackOffice:Include/NavigationPanel/Menu/Editorial/nodes.html.twig',
            array(
                'nodes' => $nodes,
                'nodeId404' => ReadNodeInterface::ERROR_404_NODE_ID,
                'nodeId503' => ReadNodeInterface::ERROR_503_NODE_ID
            )
        );
    }
}
