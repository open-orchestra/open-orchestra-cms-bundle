<?php

namespace PHPOrchestra\CMSBundle\Twig;

use PHPOrchestra\CMSBundle\DisplayField\DisplayFieldManager;

/**
 * Class DisplayFieldExtension
 */
class DisplayFieldExtension extends \Twig_Extension
{
    protected $manager;

    /**
     * @param DisplayFieldManager $manager
     */
    public function __construct(DisplayFieldManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('field_html', array($this, 'fieldHtml'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('end_field_html', array($this, 'endFieldHtml'), array('is_safe' => array('html'))),
        );
    }

    /**
     * @param string $fieldType
     *
     * @return string
     */
    public function fieldHtml($fieldType)
    {
        return $this->manager->getHtmlField($fieldType);
    }

    /**
     * @param string $fieldType
     *
     * @return string
     */
    public function endFieldHtml($fieldType)
    {
        return $this->manager->getHtmlEnd($fieldType);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'field';
    }
}
