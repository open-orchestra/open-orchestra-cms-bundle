<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\ThemeCollectionFacade;

/**
 * Class ThemeCollectionTransformer
 */
class ThemeCollectionTransformer extends AbstractTransformer
{
    /**
     * @param \Doctrine\Common\Collections\Collection $themeCollection
     *
     * @return \OpenOrchestra\BaseApi\Facade\FacadeInterface
     */
    public function transform($themeCollection)
    {
        $facade = new ThemeCollectionFacade();

        foreach ($themeCollection as $theme) {
            $facade->addTheme($this->getTransformer('theme')->transform($theme));
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_theme_list',
            array()
        ));

        $facade->addLink('_self_add', $this->generateRoute(
            'open_orchestra_backoffice_theme_new',
            array()
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'theme_collection';
    }
}
