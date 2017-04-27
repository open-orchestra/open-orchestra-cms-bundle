<?php

namespace OpenOrchestra\GroupBundle\Repository;

use OpenOrchestra\Backoffice\Repository\GroupRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Class GroupRepository
 */
class GroupRepository extends AbstractAggregateRepository implements GroupRepositoryInterface
{
    /**
     * @param PaginateFinderConfiguration $configuration
     * @param array                       $siteIds
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
     * @param array $siteIds
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
     * @param array                       $siteIds
     *
     * @return int
     */
    public function countWithFilter(PaginateFinderConfiguration $configuration, array $siteIds)
    {
        $qa = $this->createQueryWithFilter($configuration, $siteIds);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param array $groupIds
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function removeGroups(array $groupIds)
    {
        array_walk($groupIds, function(&$item) {$item = new \MongoId($item);});

        $qb = $this->createQueryBuilder();
        $qb->remove()
        ->field('id')->in($groupIds)
        ->getQuery()
        ->execute();
    }

    /**
     * @param SiteInterface $site
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function softDeleteGroupsBySite(SiteInterface $site)
    {
        $this->createQueryBuilder()
            ->updateMany()
            ->field('site')->equals($site)
            ->field('deleted')->set(true)
            ->getQuery()
            ->execute();
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
    ) {
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
        $qa->match(
            array('site.$id' => array('$in' => $siteIds)),
            array('deleted' => false)
        );

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

    /**
     * @param string $perimeterType
     * @param string $oldItem
     * @param string $newItem
     * @param string $siteId
     */
    public function updatePerimeterItem($perimeterType, $oldItem, $newItem, $siteId)
    {
        $perimeterKey = 'perimeters.' . $perimeterType . '.items';

        $this->createQueryBuilder()
            ->updateMany()
            ->field('site.$id')->equals(new \MongoId($siteId))
            ->field($perimeterKey)->equals($oldItem)
            ->field($perimeterKey)->push($newItem)
            ->getQuery()
            ->execute();

        $this->removeItemFromPerimeter($perimeterKey, $oldItem, $siteId);
    }

    /**
     * @param string $perimeterType
     * @param string $item
     * @param string $siteId
     */
    public function removePerimeterItem($perimeterType, $item, $siteId)
    {
        $this->removeItemFromPerimeter('perimeters.' . $perimeterType . '.items', $item, $siteId);
    }

    /**
     * @param string $perimeterKey
     * @param string $item
     * @param string $siteId
     */
    protected function removeItemFromPerimeter($perimeterKey, $item, $siteId)
    {
        $this->createQueryBuilder()
            ->updateMany()
            ->field('site.$id')->equals(new \MongoId($siteId))
            ->field($perimeterKey)->pull($item)
            ->getQuery()
            ->execute();
    }
}
