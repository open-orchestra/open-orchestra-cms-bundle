<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\SiteCollectionFacade;

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
        $facade = new SiteCollectionFacade();

        foreach ($siteCollection as $site) {
            $facade->addSite($this->getTransformer('site')->transform($site));
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_site_list',
            array()
        ));

        $facade->addLink('_self_add', $this->generateRoute(
            'open_orchestra_backoffice_site_new',
            array()
        ));

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
