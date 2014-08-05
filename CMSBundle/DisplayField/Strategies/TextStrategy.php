<?php

namespace PHPOrchestra\CMSBundle\DisplayField\Strategies;

use PHPOrchestra\CMSBundle\DisplayField\DisplayFieldInterface;

/**
 * Class TextStrategy
 */
class TextStrategy implements DisplayFieldInterface
{
    protected $displayedField;
    protected $class;

    /**
     * @param array $displayedField
     */
    public function __construct(array $displayedField)
    {
        $this->displayedField = $displayedField;
        $this->class = 'field';
    }

    /**
     * @param string $fieldName
     *
     * @return boolean
     */
    public function support($fieldName)
    {
        foreach ($this->displayedField as $fieldType) {
            if (preg_match('/[_]'.$fieldType.'$/', $fieldName)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'txt';
    }

    /**
     * @return string
     */
    public function getHtmlField()
    {
        return "<li class=".$this->class.">";
    }

    /**
     * @return string
     */
    public function getHtmlEnd()
    {
        return "</li>";
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
