<?php

namespace PHPOrchestra\CMSBundle\Test\Mock;

/**
 * Description of Site
 *
 * @author Noël GILAIN <noel.gilain@businessdecision.com>
 */
class Site
{
    private $id = null;
    private $domain = null;

    public function __construct($domain = '')
    {
        $this->id = new \mongoId();
        $this->domain = $domain;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getDomain()
    {
        return $this->domain;
    }
}
