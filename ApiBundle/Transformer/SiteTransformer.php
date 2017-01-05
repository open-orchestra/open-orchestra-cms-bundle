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
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($site)
    {
        if (!$site instanceof SiteInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $site->getId();
        $facade->siteId = $site->getSiteId();
        $facade->name = $site->getName();

        if ($this->hasGroup(CMSGroupContext::THEME)) {
            $facade->theme = $this->getTransformer('theme')->transform($site->getTheme());
        }

        if ($this->hasGroup(CMSGroupContext::SITE_MAIN_ALIAS)) {
            $facade->mainAlias = $this->getTransformer('site_alias')->transform($site->getMainAlias());
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
    public function getName()
    {
        return 'site';
    }
}
