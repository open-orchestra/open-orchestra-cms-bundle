<?php

namespace OpenOrchestra\Backoffice\LeftPanel\Strategies;

use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class TreeNodesPanel
 */
class TreeNodesPanelStrategy extends AbstractLeftPaneStrategy
{
    /**
     * @var NodeRepositoryInterface
     */
    protected $nodeRepository;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(NodeRepositoryInterface $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @return string
     */
    public function show()
    {
        $nodes = $this->nodeRepository->findLastVersionBySiteId();

        return $this->render(
            'OpenOrchestraBackofficeBundle:Tree:showTreeNodes.html.twig',
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
        return 'nodes';
    }
}
