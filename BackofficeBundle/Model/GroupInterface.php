<?php

namespace OpenOrchestra\BackofficeBundle\Model;

use FOS\UserBundle\Model\GroupInterface as BaseGroupInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Interface GroupInterface
 */
interface GroupInterface extends BaseGroupInterface
{
    /**
     * @param SiteInterface $site
     */
    public function setSite(SiteInterface $site);

    /**
     * @return ReadSiteInterface
     */
    public function getSite();
}
