<?php

namespace OpenOrchestra\Backoffice\LeftPanel\Strategies;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class GeneralNodesPanel
 */
class GeneralNodesPanelStrategy extends AbstractLeftPanelStrategy
{
    const ROLE_ACCESS_GENERAL_NODE = 'ROLE_ACCESS_GENERAL_NODE';

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
        $nodes = $this->nodeRepository->findLastVersionBySiteId($siteId, NodeInterface::TYPE_GENERAL);

        return $this->render(
            'OpenOrchestraBackofficeBundle:Tree:showGeneralTreeNodes.html.twig',
            array(
                'nodes' => $nodes
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
    public function getName()
    {
        return 'generale_node';
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return self::ROLE_ACCESS_GENERAL_NODE;
    }
}
