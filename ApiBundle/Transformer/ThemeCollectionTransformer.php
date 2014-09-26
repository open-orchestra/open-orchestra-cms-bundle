<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\ThemeCollectionFacade;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

        $facade->addLink('_self', $this->getRouter()->generate(
            'php_orchestra_api_theme_list',
            array(),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));

        $facade->addLink('_self_add', $this->getRouter()->generate(
            'php_orchestra_backoffice_theme_new',
            array(),
            UrlGeneratorInterface::ABSOLUTE_URL
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
