<?php

namespace PHPOrchestra\CMSBundle\Test\Mock;

/**
 * Description of TestContentType
 *
 * @author Noël GILAIN <noel.gilain@businessdecision.com>
 */
class TestContentType
{
    public function __construct($fields = array(), /*$customFieldsIndex = array(),*/ $newField = '')
    {
        $this->setFields(json_encode($fields));
        $this->newField = $newField;
    }
    
    public function getFields()
    {
        return $this->fields;
    }
    
    public function setFields($fields)
    {
        $this->fields = $fields;
    }
}
