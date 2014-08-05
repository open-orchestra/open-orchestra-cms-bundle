<?php

namespace PHPOrchestra\CMSBundle\DisplayField\Strategies;

use PHPOrchestra\CMSBundle\DisplayField\DisplayFieldInterface;

/**
 * Class ImageStrategy
 */
class ImageStrategy implements DisplayFieldInterface
{
    protected $class = 'fieldImage';

    /**
     * @param string $fieldName
     *
     * @return boolean
     */
    public function support($fieldName)
    {
        if (preg_match('/[_]'.$this->getName().'$/', $fieldName)) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'img';
    }

    /**
     * @return string
     */
    public function getHtmlField()
    {
       return "<li class=".$this->class."><img src=";
    }

    /**
     * @return string
     */
    public function getHtmlEnd()
    {
        return "></img></li>";
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
