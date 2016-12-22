<?php

namespace OpenOrchestra\Backoffice\GeneratePerimeter\Strategy;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;

/**
 * Class NodeGeneratePerimeterStrategy
 */
class NodeGeneratePerimeterStrategy implements GeneratePerimeterStrategyInterface
{
    protected $nodeRepository;
    protected $contextManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        CurrentSiteIdInterface $contextManager

    ) {
        $this->nodeRepository = $nodeRepository;
        $this->contextManager = $contextManager;
    }

    /**
     * Return the supported perimeter type
     *
     * @return string
     */
    public function getType()
    {
        return NodeInterface::ENTITY_TYPE;
    }

    /**
     * Generate perimeter
     *
     * @return array
     */
    public function generatePerimeter()
    {
        $treeNodes = $this->nodeRepository->findTreeNode($this->contextManager->getCurrentSiteId(), $this->contextManager->getUserCurrentSiteDefaultLanguage(), NodeInterface::ROOT_NODE_ID);
        $treeNodes = $this->formatPerimeter($treeNodes[0]);

        return $treeNodes;
    }

    /**
     * get perimeter configuration
     *
     * @return array
     */
    public function getPerimeterConfiguration()
    {
        $treeNodes = $this->nodeRepository->findTreeNode($this->contextManager->getCurrentSiteId(), $this->contextManager->getUserCurrentSiteDefaultLanguage(), NodeInterface::ROOT_NODE_ID);
        $treeNodes = $this->formatConfiguration($treeNodes[0]);

        return $treeNodes;
    }

    /**
     * format perimeter
     *
     * @param array $treeNodes
     *
     * @return array
     */
    protected function formatPerimeter(&$treeNodes)
    {
        $path = $treeNodes['node']['path'];
        $treeNodes[] = $path;
        unset($treeNodes['node']);
        foreach ($treeNodes['child'] as &$child) {
            $treeNodes = array_merge($treeNodes, $this->formatPerimeter($child));
        }
        unset($treeNodes['child']);

        return $treeNodes;
    }

    /**
     * format configuration
     *
     * @param array $treeNodes
     *
     * @return array
     */
    protected function formatConfiguration(&$treeNodes)
    {
        $treeNodes['node'] = array('path' => $treeNodes['node']['path'], 'name' => $treeNodes['node']['name']);
        if (count($treeNodes['child']) == 0) {
            unset($treeNodes['child']);
        } else {
            foreach ($treeNodes['child'] as &$child) {
                $child = $this->formatConfiguration($child);
            }
        }

        return $treeNodes;
    }
}
