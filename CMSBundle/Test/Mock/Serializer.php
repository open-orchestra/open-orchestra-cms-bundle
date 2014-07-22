<?php

namespace PHPOrchestra\CMSBundle\Test\Mock;

/**
 * Description of Serializer
 *
 * @author Noël GILAIN <noel.gilain@businessdecision.com>
 */
class Serializer
{
    public function deserialize($string, $type, $format)
    {
        return json_decode($string);
    }
    
    public function serialize($object, $format)
    {
        return json_encode($object);
    }
}
