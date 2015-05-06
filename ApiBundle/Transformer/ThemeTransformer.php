<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\ThemeFacade;
use OpenOrchestra\ModelInterface\Model\ThemeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class ThemeTransformer
 */
class ThemeTransformer extends AbstractTransformer
{
    /**
     * @param ThemeInterface $mixed
     *
     * @return ThemeFacade
     */
    public function transform($mixed)
    {
        $facade = new ThemeFacade();

        $facade->id = $mixed->getId();
        $facade->name = $mixed->getName();

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_theme_show',
            array('themeId' => $mixed->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_theme_delete',
            array('themeId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_theme_form',
            array('themeId' => $mixed->getId())
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
