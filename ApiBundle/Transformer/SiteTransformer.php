<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ApiBundle\Context\CMSGroupContext;

/**
 * Class SiteTransformer
 */
class SiteTransformer extends AbstractTransformer
{
    /**
     * @param SiteInterface $site
     * @param array         $params
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($site, array $params = array())
    {
        if (!$site instanceof SiteInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $site->getId();
        $facade->siteId = $site->getSiteId();
        $facade->name = $site->getName();

        if ($this->hasGroup(CMSGroupContext::SITE_MAIN_ALIAS)) {
            $facade->mainAlias = $this->getContext()->transform('site_alias', $site->getMainAlias());
        }

        if ($this->hasGroup(CMSGroupContext::SITE_ALIASES)) {
            $facade->mainAlias = $this->getContext()->transform('site_alias', $site->getMainAlias());
        }

        foreach ($site->getLanguages() as $language) {
            $facade->addLanguage($language);
        }

        if ($this->hasGroup(CMSGroupContext::BLOCKS)) {
            foreach ($site->getBlocks() as $value) {
                $facade->addBlocks($value);
            }
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function isCached()
    {
        return $this->hasGroup(CMSGroupContext::SITE);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site';
    }
}
