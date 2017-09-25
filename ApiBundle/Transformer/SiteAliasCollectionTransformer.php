<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class SiteCollectionTransformer
 */
class SiteCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $siteAliasCollection
     * @param array      $params
     *
     * @return FacadeInterface
     */
    public function transform($siteAliasCollection, array $params = array())
    {
        $facade = $this->newFacade();

        foreach ($siteAliasCollection as $siteAlias) {
            $facade->addSiteAlias($this->getContext()->transform('site_alias', $siteAlias));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_alias_collection';
    }
}
