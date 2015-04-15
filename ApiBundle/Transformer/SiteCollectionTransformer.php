<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\SiteCollectionFacade;

/**
 * Class SiteCollectionTransformer
 */
class SiteCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new SiteCollectionFacade();

        foreach ($mixed as $site) {
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

        $facade->addLink('_translate', $this->generateRoute(
            'open_orchestra_api_translate'
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
