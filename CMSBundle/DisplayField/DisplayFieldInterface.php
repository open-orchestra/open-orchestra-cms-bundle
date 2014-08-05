<?php

namespace PHPOrchestra\CMSBundle\DisplayField;

/**
 * Interface DisplayFieldInterface
 */
Interface DisplayFieldInterface
{
    /**
     * @param string $fieldName
     *
     * @return boolean
     */
    public function support($fieldName);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getHtmlField();

    /**
     * @return string
     */
    public function getHtmlEnd();

    /**
     * @param string $class
     */
    public function setClass($class);

    /**
     * @return string
     */
    public function getClass();
}
