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
     * @param Collection $siteCollection
     *
     * @return FacadeInterface
     */
    public function transform($siteCollection)
    {
        $facade = $this->newFacade();

        foreach ($siteCollection as $site) {
            $facade->addSite($this->getTransformer('site')->transform($site));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_collection';
    }
}
