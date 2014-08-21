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

        foreach ($mixed->getLanguages() as $language) {
            $facade->addLanguage($language);
        }

        foreach ($mixed->getBlocks() as $key => $value) {
            $facade->addBlocks($key, $value);
        }

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
