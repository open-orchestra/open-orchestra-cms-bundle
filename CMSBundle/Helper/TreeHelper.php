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
        	if(array_key_exists($l_pid, $node)){
	            $parents[$node[$l_pid]][] = $node;
	            if(!array_key_exists($node[$l_pid], $values)){
	            	$root = $node;
	            }
        	}
        }
        
        if (!empty($parents) && isset($root)) {
        	$tree = self::createRecTree($parents, array($root));
        } else {
        	return $values;
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
            'class' => array_key_exists('deleted', $node) && $node['deleted']? 'deleted':'',
            'text' => $node['name']
        );
    }

    /**
     * @param array $list
     * @param array $parent
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
