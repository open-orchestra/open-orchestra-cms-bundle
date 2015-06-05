<?php

namespace OpenOrchestra\GroupBundle\Document;

use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\UserBundle\Document\Group as BaseGroup;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *  collection="group_document",
 *  repositoryClass="OpenOrchestra\GroupBundle\Repository\GroupRepository"
 * )
 */
class Group extends BaseGroup implements GroupInterface
{
    /**
     * @ODM\ReferenceOne(targetDocument="OpenOrchestra\ModelInterface\Model\ReadSiteInterface")
     */
    protected $site;

    /**
     * @param ReadSiteInterface $site
     */
    public function setSite(ReadSiteInterface $site)
    {
        $this->site = $site;
    }

    /**
     * @return ReadSiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }
}
