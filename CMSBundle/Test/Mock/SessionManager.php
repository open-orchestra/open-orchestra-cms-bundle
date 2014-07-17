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
        $value = '';
        
        if (isset($this->storage[$key])) {
            $value = $this->storage[$key];
        }
        
        return $value;
    }
}
