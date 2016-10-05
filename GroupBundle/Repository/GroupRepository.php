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
     * Find all groups linked to site with $id
     *
     * @param string $id   The site id
     * @return array
     */
    public function findAllWithSiteId($id)
    {
        $qa = $this->createAggregationQuery();
        $filter = array(
            'site.$id' => new \MongoId($id)
        );
        $qa->match($filter);
        return $this->hydrateAggregateQuery($qa);
    }
}
