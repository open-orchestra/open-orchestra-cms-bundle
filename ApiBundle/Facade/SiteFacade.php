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
    protected $languages = array();

    /**
     * @Serializer\Type("array<string>")
     */
    protected $blocks = array();

    /**
     * @Serializer\Type("PHPOrchestra\ApiBundle\Facade\ThemeFacade")
     */
    public $theme;

    /**
     * @param string $value
     */
    public function addBlocks($value)
    {
        $this->blocks[] = $value;
    }

    /**
     * @param string $value
     */
    public function addLanguage($value)
    {
        $this->languages[] = $value;
    }
}
