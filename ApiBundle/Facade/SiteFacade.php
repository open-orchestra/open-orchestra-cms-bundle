<?php

namespace OpenOrchestra\ApiBundle\Facade;

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
    public $name;

    /**
     * @Serializer\Type("string")
     */
    public $metaKeywords;

    /**
     * @Serializer\Type("string")
     */
    public $metaDescription;

    /**
     * @Serializer\Type("boolean")
     */
    public $metaIndex = false;

    /**
     * @Serializer\Type("boolean")
     */
    public $metaFollow = false;

    /**
     * @Serializer\Type("array<string>")
     */
    protected $languages = array();

    /**
     * @Serializer\Type("array<string>")
     */
    protected $blocks = array();

    /**
     * @Serializer\Type("OpenOrchestra\ApiBundle\Facade\ThemeFacade")
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
