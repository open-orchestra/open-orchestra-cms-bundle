<?php

namespace OpenOrchestra\GroupBundle\Repository;

use OpenOrchestra\Backoffice\Repository\GroupRepositoryInterface;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;

/**
 * Class GroupRepository
 */
class GroupRepository extends AbstractAggregateRepository implements GroupRepositoryInterface
{
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

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param array|null                  $siteId
     *
     * @return array
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration, array $siteIds = null)
    {
        $order = $configuration->getOrder();
        $qa = $this->createQueryWithFilter($configuration, $siteIds, $order);

        $qa->skip($configuration->getSkip());
        $qa->limit($configuration->getLimit());

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param array|null $siteId
     *
     * @return int
     */
    public function count(array $siteIds = null)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIds($siteIds);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param array|null                  $siteId
     *
     * @return int
     */
    public function countWithFilter(PaginateFinderConfiguration $configuration, array $siteIds = null)
    {
        $qa = $this->createQueryWithFilter($configuration, $siteIds);

        return $this->countDocumentAggregateQuery($qa);
    }


    /**
     * @param PaginateFinderConfiguration $configuration
     * @param array|null                  $siteIds
     * @param array                       $order
     *
     * @return Stage
     */
    protected function createQueryWithFilter(
        PaginateFinderConfiguration $configuration,
        $siteIds = null,
        $order = array()
    ){
        $qa = $this->createAggregationQueryBuilderWithSiteIds($siteIds);
        $filters = $this->getFilterSearch($configuration);
        if (!empty($filters)) {
            $qa->match($filters);
        }

        if (!empty($order)) {
            $qa->sort($order);
        }

        return $qa;
    }

    /**
     * @param array|null $siteIds
     *
     * @return Stage
     */
    protected function createAggregationQueryBuilderWithSiteIds($siteIds = null)
    {
        $qa = $this->createAggregationQuery();
        if (!is_null($siteIds)) {
            $qa->match(array('site.$id' => $siteIds));
        }

        return $qa;
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    protected function getFilterSearch(PaginateFinderConfiguration $configuration) {
        $filter = array();
        $label = $configuration->getSearchIndex('label');
        if (null !== $label && $label !== '') {
            $filter['label'] = new MongoRegex('/.*'.$label.'.*/i');
        }

        $siteId = $configuration->getSearchIndex('siteId');
        if (null !== $siteId && $siteId !== '') {
            $filter['site.$i'] = new MongoRegex('/.*'.$siteId.'.*/i');
        }

        return $filter;
    }
}
