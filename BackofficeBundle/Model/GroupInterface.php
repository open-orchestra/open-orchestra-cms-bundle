<?php

namespace OpenOrchestra\BackofficeBundle\Model;

use FOS\UserBundle\Model\GroupInterface as BaseGroupInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\ModelInterface\Model\TranslatedValueContainerInterface;

/**
 * Interface GroupInterface
 */
interface GroupInterface extends BaseGroupInterface, TranslatedValueContainerInterface
{
    /**
     * @param ReadSiteInterface|null $site
     */
    public function setSite(ReadSiteInterface $site = null);

    /**
     * @return ReadSiteInterface|null
     */
    public function getSite();
}
