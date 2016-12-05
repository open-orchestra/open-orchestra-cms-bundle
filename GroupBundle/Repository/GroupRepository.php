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
        $qb = $this->getDocumentManager()->createQueryBuilder('OpenOrchestra\ModelBundle\Document\Site');
        $qb->field('deleted')->equals(false);
        $sites = $qb->getQuery()->toArray();

        $siteIds = (is_array($siteIds)) ? array_intersect(array_keys($sites), $siteIds) : array_keys($sites);

        $qa = $this->createAggregationQuery();
        if (!is_null($siteIds)) {
            foreach ($siteIds as $key => $siteId) {
                $siteIds[$key] = new \MongoId($siteId);
            }
            $qa->match(array('site.$id' => array('$in' => $siteIds)));
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
