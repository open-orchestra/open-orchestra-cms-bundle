<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\SiteCollectionFacade;

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
            'php_orchestra_api_site_list',
            array()
        ));

        $facade->addLink('_self_add', $this->generateRoute(
            'php_orchestra_backoffice_site_new',
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
