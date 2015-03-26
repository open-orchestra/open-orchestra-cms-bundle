<?php

namespace OpenOrchestra\BackofficeBundle\Model;

use FOS\UserBundle\Model\GroupInterface as BaseGroupInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;

/**
 * Interface GroupInterface
 */
interface GroupInterface extends BaseGroupInterface
{
    /**
     * @param ReadSiteInterface $site
     */
    public function setSite(ReadSiteInterface $site);

    /**
     * @return ReadSiteInterface
     */
    public function getSite();
}
