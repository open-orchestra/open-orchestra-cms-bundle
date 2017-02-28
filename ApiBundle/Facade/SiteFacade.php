<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

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
     * @Serializer\Type("array<string>")
     */
    protected $languages = array();

    /**
     * @Serializer\Type("array<string>")
     */
    protected $blocks;

    /**
     * @Serializer\Type("OpenOrchestra\ApiBundle\Facade\SiteAliasFacade")
     */
    public $mainAlias;

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
