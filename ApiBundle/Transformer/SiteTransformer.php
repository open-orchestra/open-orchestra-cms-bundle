<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\SiteFacade;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class SiteTransformer
 */
class SiteTransformer extends AbstractTransformer
{
    /**
     * @param SiteInterface $site
     *
     * @return \OpenOrchestra\BaseApi\Facade\FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($site)
    {
        if (!$site instanceof SiteInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = new SiteFacade();

        $facade->id = $site->getId();
        $facade->siteId = $site->getSiteId();
        $facade->name = $site->getName();
        $facade->metaKeywords = $site->getMetaKeywords();
        $facade->metaDescription = $site->getMetaDescription();
        $facade->metaIndex = $site->getMetaIndex();
        $facade->metaFollow = $site->getMetaFollow();
        $facade->theme = $this->getTransformer('theme')->transform($site->getTheme());

        foreach ($site->getLanguages() as $language) {
            $facade->addLanguage($language);
        }

        foreach ($site->getBlocks() as $value) {
            $facade->addBlocks($value);
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_site_show',
            array('siteId' => $site->getSiteId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_site_delete',
            array('siteId' => $site->getSiteId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_site_form',
            array('siteId' => $site->getSiteId())
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
