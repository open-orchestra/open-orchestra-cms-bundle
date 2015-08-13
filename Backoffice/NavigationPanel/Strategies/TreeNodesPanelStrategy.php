<?php

namespace OpenOrchestra\Backoffice\NavigationPanel\Strategies;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;

/**
 * Class TreeNodesPanel
 */
class TreeNodesPanelStrategy extends AbstractNavigationPanelStrategy
{
    const ROLE_ACCESS_TREE_NODE = 'ROLE_ACCESS_TREE_NODE';

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
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, CurrentSiteIdInterface $currentSiteManager)
    {
        $this->nodeRepository = $nodeRepository;
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @return string
     */
    public function show()
    {
        $siteId = $this->currentSiteManager->getCurrentSiteId();
        $nodes = $this->nodeRepository->findLastVersionBySiteId($siteId);

        return $this->render(
            'OpenOrchestraBackofficeBundle:Tree:showTreeNodes.html.twig',
            array(
                'nodes' => $nodes,
                'nodeId404' => ReadNodeInterface::ERROR_404_NODE_ID,
                'nodeId503' => ReadNodeInterface::ERROR_503_NODE_ID
            )
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return self::EDITORIAL;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return self::ROLE_ACCESS_TREE_NODE;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nodes';
    }

}
