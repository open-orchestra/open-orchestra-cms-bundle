<?php

namespace PHPOrchestra\CMSBundle\Helper;

/**
 * Class TreeHelper
 */
class TreeHelper
{
    /**
     * @param array  $values
     * @param string $l_id
     * @param string $l_pid
     *
     * @return array
     */
    public static function createTree($values, $l_id = '_id', $l_pid = 'parentId')
    {
        $tree = array();

        $newValues = array();
        foreach ($values as $node) {
            $newValues[$node[$l_id]] = $node;
        }
        $values = $newValues;

        $parents = array();
        foreach ($values as $node) {
            if ('0' !== $node[$l_pid]) {
                $parents[$node[$l_pid]][] = $node[$l_id];
            }
        }

        if (!empty($parents)) {
            foreach ($parents as $parentId => $sons) {
                $tmpTree = self::createTreeFromNode($values[$parentId]);
                foreach ($sons as $sonId) {
                    $tmpTree['sublinks'][] = self::createTreeFromNode($values[$sonId]);
                }
                $tree[] = $tmpTree;
            }
        } else {
            foreach ($values as $node) {
                $tree[] = self::createTreeFromNode($node);
            }

        }

        return $tree;
    }

    /**
     * @param array $node
     *
     * @return array
     */
    public static function createTreeFromNode($node)
    {
        return array(
            'id' => $node['_id'],
            'class' => $node['deleted']? 'deleted':'',
            'text' => $node['name']
        );
    }

    /**
     * @param array $list
     * @param array $parent
     *
     * @return array
     */
    public static function createRecTree(&$list, $parent)
    {
        $tree = array();
        foreach ($parent as $l) {
            if (isset($list[$l['id']])) {
                $l['sublinks'] = TreeHelper::createRecTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        }

        return $tree;
    }
}
