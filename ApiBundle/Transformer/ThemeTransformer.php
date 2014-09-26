<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\ThemeFacade;
use PHPOrchestra\ModelBundle\Model\ThemeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

        $facade->name = $mixed->getName();

        $facade->addLink('_self', $this->getRouter()->generate(
            'php_orchestra_api_theme_show',
            array('themeId' => $mixed->getId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
        $facade->addLink('_self_delete', $this->getRouter()->generate(
            'php_orchestra_api_theme_delete',
            array('themeId' => $mixed->getId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
        $facade->addLink('_self_form', $this->getRouter()->generate(
            'php_orchestra_backoffice_theme_form',
            array('themeId' => $mixed->getId()),
            UrlGeneratorInterface::ABSOLUTE_URL
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
