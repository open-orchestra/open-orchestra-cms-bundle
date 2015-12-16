<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Model\ThemeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;

/**
 * Class ThemeTransformer
 */
class ThemeTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param ThemeInterface $theme
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($theme)
    {
        if (!$theme instanceof ThemeInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $theme->getId();
        $facade->name = $theme->getName();

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_THEME)) {
            $facade->addLink('_self', $this->generateRoute(
                'open_orchestra_api_theme_show',
                array('themeId' => $theme->getId())
            ));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_THEME)) {
            $facade->addLink('_self_delete', $this->generateRoute(
                'open_orchestra_api_theme_delete',
                array('themeId' => $theme->getId())
            ));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_THEME)) {
            $facade->addLink('_self_form', $this->generateRoute(
                'open_orchestra_backoffice_theme_form',
                array('themeId' => $theme->getId())
            ));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'theme';
    }
}
