<?php

namespace OpenOrchestra\Backoffice\GeneratePerimeter\Strategy;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;

/**
 * Class GeneratePerimeterStrategy
 */
abstract class GeneratePerimeterStrategy
{
    protected $contextManager;

    /**
     * @param CurrentSiteIdInterface $contextManager
     */
    public function __construct(
        CurrentSiteIdInterface $contextManager
    ) {
        $this->contextManager = $contextManager;
    }

    /**
     * @param array $tree
     *
     * @return array
     */
    public function generateTreePerimeter(array $tree)
    {
        $result = array();

        foreach ($tree as $leaf) {
            $result = array_merge($result, $this->formatPerimeter($leaf));
        }

        return $result;
    }

    /**
     * @param array $tree
     *
     * @return array
     */
    public function getTreePerimeterConfiguration($tree)
    {
        foreach ($tree as &$leaf) {
            $leaf = $this->formatConfiguration($leaf);
        }

        return $tree;
    }

    /**
     * format perimeter
     *
     * @param array $tree
     *
     * @return array
     */
    abstract protected function formatPerimeter(array $tree);

    /**
     * format configuration
     *
     * @param array $tree
     *
     * @return array
     */
    abstract protected function formatConfiguration(array $tree);
}
