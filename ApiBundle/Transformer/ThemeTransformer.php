<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeHttpException;
use OpenOrchestra\ApiBundle\Facade\ThemeFacade;
use OpenOrchestra\ModelInterface\Model\ThemeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class ThemeTransformer
 */
class ThemeTransformer extends AbstractTransformer
{
    /**
     * @param ThemeInterface $theme
     *
     * @return ThemeFacade
     *
     * @throws TransformerParameterTypeHttpException
     */
    public function transform($theme)
    {
        if (!$theme instanceof ThemeInterface) {
            throw new TransformerParameterTypeHttpException();
        }

        $facade = new ThemeFacade();

        $facade->id = $theme->getId();
        $facade->name = $theme->getName();

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_theme_show',
            array('themeId' => $theme->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_theme_delete',
            array('themeId' => $theme->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_theme_form',
            array('themeId' => $theme->getId())
        ));

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
