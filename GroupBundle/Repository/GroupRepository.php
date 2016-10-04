<?php

namespace OpenOrchestra\GroupBundle\Repository;

use OpenOrchestra\Backoffice\Repository\GroupRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class GroupRepository
 */
class GroupRepository extends AbstractAggregateRepository implements GroupRepositoryInterface
{
    use PaginationTrait;

    /**
     * Find all groups linked to a site
     *
     * @return array
     */
    public function findAllWithSite()
    {
        $qa = $this->createAggregationQuery();
        $filter = array(
            'site' => array('$ne' => null)
        );
        $qa->match($filter);

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * Find all groups linked to $siteId
     * 
     * @param string $siteId
     * @return array
     */
    public function findAllWithSiteId($siteId)
    {
        $qa = $this->createAggregationQuery();
        $filter = array(
            'site.$id' => new \MongoId($siteId)
        );
        $qa->match($filter);

        return $this->hydrateAggregateQuery($qa);
    }
}
