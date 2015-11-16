<?php

namespace OpenOrchestra\ApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class ThemeCollection
 */
class ThemeCollectionFacade extends PaginateCollectionFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'themes';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\ThemeFacade>")
     */
    protected $themes = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addTheme(FacadeInterface $facade)
    {
        $this->themes[] = $facade;
    }
}
