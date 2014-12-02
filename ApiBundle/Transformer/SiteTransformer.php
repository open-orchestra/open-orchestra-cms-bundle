<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\SiteFacade;
use PHPOrchestra\ModelBundle\Model\SiteInterface;

/**
 * Class SiteTransformer
 */
class SiteTransformer extends AbstractTransformer
{
    /**
     * @param SiteInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new SiteFacade();

        $facade->siteId = $mixed->getSiteId();
        $facade->domain = $mixed->getDomain();
        $facade->alias = $mixed->getAlias();
        $facade->defaultLanguage = $mixed->getDefaultLanguage();
        $facade->theme = $mixed->getTheme();

        foreach ($mixed->getLanguages() as $language) {
            $facade->addLanguage($language);
        }

        foreach ($mixed->getBlocks() as $value) {
            $facade->addBlocks($value);
        }

        $facade->addLink('_self', $this->generateRoute(
            'php_orchestra_api_site_show',
            array('siteId' => $mixed->getSiteId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'php_orchestra_api_site_delete',
            array('siteId' => $mixed->getSiteId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'php_orchestra_backoffice_site_form',
            array('siteId' => $mixed->getSiteId())
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site';
    }
}
