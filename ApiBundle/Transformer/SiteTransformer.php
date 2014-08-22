<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\SiteFacade;
use PHPOrchestra\ModelBundle\Model\SiteInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

        foreach ($mixed->getBlocks() as $value) {
            $facade->addBlocks($value);
        }

        $facade->addLink('_self', $this->getRouter()->generate(
            'php_orchestra_api_site_show',
            array('siteId' => $mixed->getSiteId()),
            UrlGeneratorInterface::ABSOLUTE_URL
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
