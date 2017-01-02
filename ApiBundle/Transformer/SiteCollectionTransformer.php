<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

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
     * @param FacadeInterface $facade
     * @param null $source
     *
     * @return SiteInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        $sites = array();
        $sitesFacade = $facade->getSites();
        foreach ($sitesFacade as $siteFacade) {
            $site = $this->getTransformer('site')->reverseTransform($siteFacade);
            if (null !== $site) {
                $sites[] = $site;
            }
        }

        return $sites;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_collection';
    }
}
