<?php

namespace OpenOrchestra\Backoffice\GeneratePerimeter\Strategy;

use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class NodeGeneratePerimeterStrategy
 */
class NodeGeneratePerimeterStrategy extends GeneratePerimeterStrategy implements GeneratePerimeterStrategyInterface
{
    protected $nodeRepository;
    protected $contextManager;

    /**
     * @param NodeRepositoryInterface    $nodeRepository
     * @param ContextBackOfficeInterface $contextManager
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, ContextBackOfficeInterface $contextManager)
    {
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
     * @param string $siteId
     * @return array
     */
    public function generatePerimeter($siteId)
    {
        $treeNodes = $this->nodeRepository->findTreeNode(
            $siteId,
            $this->contextManager->getSiteContributionLanguage(),
            NodeInterface::ROOT_PARENT_ID
        );

        return $this->generateTreePerimeter($treeNodes);
    }

    /**
     * get perimeter configuration
     *
     * @param string $siteId
     * @return array
     */
    public function getPerimeterConfiguration($siteId)
    {
        $treeNodes = $this->nodeRepository->findTreeNode(
            $siteId,
            $this->contextManager->getSiteContributionLanguage(),
            NodeInterface::ROOT_PARENT_ID
        );

        return $this->getTreePerimeterConfiguration($treeNodes);
    }

    /**
     * format perimeter
     *
     * @param array $treeNodes
     *
     * @return array
     */
    protected function formatPerimeter(array $treeNodes)
    {
        $path = array_key_exists('path', $treeNodes['node']) ? $treeNodes['node']['path'] : '';
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
    protected function formatConfiguration(array $treeNodes)
    {
        $treeNodes['root'] = array('path' => $treeNodes['node']['path'], 'name' => $treeNodes['node']['name']);
        unset($treeNodes['node']);
        if (count($treeNodes['child']) == 0) {
            unset($treeNodes['child']);
        } else {
            $children = $treeNodes['child'];
            unset($treeNodes['child']);
            foreach ($children as $child) {
                $treeNodes['children'][] = $this->formatConfiguration($child);
            }
        }

        return $treeNodes;
    }
}
