<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class SiteFacade
 */
class SiteFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $siteId;

    /**
     * @Serializer\Type("string")
     */
    public $domain;

    /**
     * @Serializer\Type("string")
     */
    public $alias;

    /**
     * @Serializer\Type("string")
     */
    public $defaultLanguage;

    /**
     * @Serializer\Type("array<string>")
     */
    protected  $languages = array();

    /**
     * @Serializer\Type("array<string>")
     */
    protected $blocks = array();

    /**
     * @param string $key
     * @param string $value
     */
    public function addBlocks($key, $value)
    {
        $this->blocks[$key] = $value;
    }

    /**
     * @param string $value
     */
    public function addLanguage($value)
    {
        $this->languages[] = $value;
    }
}
