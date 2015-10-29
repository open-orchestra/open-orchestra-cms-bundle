<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ApiBundle\Facade\ThemeCollectionFacade;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;

/**
 * Class ThemeCollectionTransformer
 */
class ThemeCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $themeCollection
     *
     * @return FacadeInterface
     */
    public function transform($themeCollection)
    {
        $facade = new ThemeCollectionFacade();

        foreach ($themeCollection as $theme) {
            $facade->addTheme($this->getTransformer('theme')->transform($theme));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_THEME)) {
            $facade->addLink('_self', $this->generateRoute(
                'open_orchestra_api_theme_list',
                array()
            ));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_THEME)) {
            $facade->addLink('_self_add', $this->generateRoute(
                'open_orchestra_backoffice_theme_new',
                array()
            ));
        }

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
