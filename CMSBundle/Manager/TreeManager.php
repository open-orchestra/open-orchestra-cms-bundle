<?php

namespace PHPOrchestra\CMSBundle\Manager;

use PHPOrchestra\ModelBundle\Model\NodeInterface;

/**
 * Class TreeManager
 */
class TreeManager
{
    protected $withoutParent = array();
    protected $iteration = 0;

    /**
     * @param array $nodes
     *
     * @return array
     */
    public function generateTree($nodes)
    {
        $tree = array();
        $this->withoutParent = array();
        $this->iteration = 0;
        $superParentId = count(array_filter($nodes, function($node) {
            return '-' == $node->getParentId();
        }))? '-': 'root';

        /** @var NodeInterface $node */
        foreach ($nodes as $node) {
            if ($superParentId == $node->getParentId()) {
                $tree[] = array('node' => $node, 'child' => array());
            } else {
                $this->withoutParent[$this->iteration][] = $node;
            }
        }

        while (!empty($this->withoutParent[$this->iteration]) && $this->iteration < 10) {
            $this->iteration += 1;
            foreach ($this->withoutParent[$this->iteration - 1] as $node) {
                $tree = $this->findParent($tree, $node);
            }
        }

        if ($this->iteration > 9) {
            $rootNodePresent = count($tree);
            foreach ($this->withoutParent[$this->iteration] as $node) {
                if ($rootNodePresent > 0) {
                    $tree[0]['child'][] = array('node' => $node, 'child' => array());
                } else {
                    $tree[] = array('node' => $node, 'child' => array());
                }
            }
        }

        return $tree;
    }

    /**
     * @param array         $tree
     * @param NodeInterface $node
     *
     * @return array
     */
    protected function findParent($tree, $node)
    {
        if (empty($tree)) {
            $this->withoutParent[$this->iteration][] = $node;

            return $tree;
        }
        foreach ($tree as $key => $nodeElement) {
            if ($nodeElement['node']->getNodeId() == $node->getParentId()) {
                $tree[$key]['child'][] = array('node' => $node, 'child' => array());

                return $tree;
            }
        }
        foreach ($tree as $key => $nodeElement) {
            if ($nodeElement['node']->getNodeId() != $node->getParentId()) {
                $tree[$key]['child'] = $this->findParent($nodeElement['child'], $node);
            }
        }

        return $tree;
    }
}
