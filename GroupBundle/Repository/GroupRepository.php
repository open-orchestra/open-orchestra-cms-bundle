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
     * @param array                       $siteId
     *
     * @return array
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration, array $siteIds)
    {
        $order = $configuration->getOrder();
        $qa = $this->createQueryWithFilter($configuration, $siteIds, $order);

        $qa->skip($configuration->getSkip());
        $qa->limit($configuration->getLimit());

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param array $siteId
     *
     * @return int
     */
    public function count(array $siteIds)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIds($siteIds);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param array                       $siteId
     *
     * @return int
     */
    public function countWithFilter(PaginateFinderConfiguration $configuration, array $siteIds)
    {
        $qa = $this->createQueryWithFilter($configuration, $siteIds);

        return $this->countDocumentAggregateQuery($qa);
    }


    /**
     * @param PaginateFinderConfiguration $configuration
     * @param array                       $siteIds
     * @param array                       $order
     *
     * @return Stage
     */
    protected function createQueryWithFilter(
        PaginateFinderConfiguration $configuration,
        array $siteIds,
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
     * @param array $siteIds
     *
     * @return Stage
     */
    protected function createAggregationQueryBuilderWithSiteIds(array $siteIds)
    {
        $qa = $this->createAggregationQuery();
        foreach ($siteIds as $key => $siteId) {
            $siteIds[$key] = new \MongoId($siteId);
        }
        $qa->match(array('site.$id' => array('$in' => $siteIds)));

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
        $language = $configuration->getSearchIndex('language');
        if (null !== $label && $label !== '' && null !== $language && $language !== '' ) {
            $filter['labels.' . $language] = new \MongoRegex('/.*'.$label.'.*/i');
        }
        $siteId = $configuration->getSearchIndex('site');
        if (null !== $siteId && $siteId !== '') {
            $filter['site.$id'] = new \MongoId($siteId);
        }

        return $filter;
    }
}
