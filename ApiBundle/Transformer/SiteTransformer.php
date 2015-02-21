<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\SiteFacade;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

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

        $facade->id = $mixed->getId();
        $facade->siteId = $mixed->getSiteId();
        $facade->name = $mixed->getName();
        $facade->metaKeywords = $mixed->getMetaKeywords();
        $facade->metaDescription = $mixed->getMetaDescription();
        $facade->metaIndex = $mixed->getMetaIndex();
        $facade->metaFollow = $mixed->getMetaFollow();
        $facade->theme = $this->getTransformer('theme')->transform($mixed->getTheme());

        foreach ($mixed->getLanguages() as $language) {
            $facade->addLanguage($language);
        }

        foreach ($mixed->getBlocks() as $value) {
            $facade->addBlocks($value);
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_site_show',
            array('siteId' => $mixed->getSiteId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_site_delete',
            array('siteId' => $mixed->getSiteId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_site_form',
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
