<?php

namespace PHPOrchestra\CMSBundle\Test\Mock;

/**
 * Description of SessionManager
 *
 * @author Noël GILAIN <noel.gilain@businessdecision.com>
 */
class SessionManager
{
    private $storage = array();
    
    public function set($key, $value)
    {
        $this->storage[$key] = $value;
    }
    
    public function get($key)
    {
        if (array_key_exists($key, $this->storage)) {
            return $this->storage[$key];
        }
        
        return '';
    }
}
