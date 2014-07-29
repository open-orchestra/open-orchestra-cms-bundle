<?php

namespace PHPOrchestra\CMSBundle\Helper;
use PHPOrchestra\ModelBundle\Document\Node;

/**
 * Class TreeHelper
 */
class TreeHelper
{
    /**
     * @param array  $values
     *
     * @return array
     */
    public static function createTreeFromObject($values)
    {
    }

    /**
     * @param array $list
     * @param array $parent
     *
     * @return array
     */
    public static function createRecTreeFromObject($list, $parent)
    {
    }

    /**
     * @param array  $values
     * @param string $l_id
     * @param string $l_pid
     *
     * @return array
     */
    public static function createTree($values, $l_id = '_id', $l_pid = 'parentId')
    {
        $newValues = array();
        foreach ($values as $node) {
            $newValues[$node[$l_id]] = $node;
        }
        $values = $newValues;

        $parents = array();
        foreach ($values as $node) {
            if(array_key_exists($l_pid, $node)){
                $parents[$node[$l_pid]][] = $node;
                if(!array_key_exists($node[$l_pid], $values)){
                    $root = $node;
                }
            }
        }

        if (!empty($parents) && isset($root)) {
            return self::createRecTree($parents, array($root));
        } else {
            return $values;
        }
    }

    /**
     * @param array $list
     * @param array $parent
     * @param string $l_id
     *
     * @return array
     */
    public static function createRecTree(&$list, $parent, $l_id = '_id')
    {
        $tree = array();
        foreach ($parent as $l) {
            if (isset($list[$l[$l_id]])) {
                $l['sublinks'] = TreeHelper::createRecTree($list, $list[$l[$l_id]], $l_id);
            }
            $tree[] = $l;
        }

        return $tree;
    }
}
