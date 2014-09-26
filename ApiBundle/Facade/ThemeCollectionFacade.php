<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class ThemeCollection
 */
class ThemeCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'themes';

    /**
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\ThemeFacade>")
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
