<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\ThemeCollectionFacade;

/**
 * Class ThemeCollectionTransformer
 */
class ThemeCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ThemeCollectionFacade();

        foreach ($mixed as $theme) {
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
