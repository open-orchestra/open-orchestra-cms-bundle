<?php

namespace OpenOrchestra\BackofficeBundle\Document;

use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\UserBundle\Document\Group as BaseGroup;

/**
 * Class Group
 */
class Group extends BaseGroup implements GroupInterface
{
    /**
     * @MongoDB\ReferenceOne(targetDocument="OpenOrchestra\ModelInterface\Model\SiteInterface")
     */
    protected $site;

    /**
     * @param SiteInterface $site
     */
    public function setSite(SiteInterface $site)
    {
        $this->site = $site;
    }

    /**
     * @return SiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }

}
