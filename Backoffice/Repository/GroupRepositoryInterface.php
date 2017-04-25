<?php

namespace OpenOrchestra\Backoffice\Repository;

use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;

/**
 * Interface GroupRepositoryInterface
 */
interface GroupRepositoryInterface
{
    /**
     * @param PaginateFinderConfiguration $configuration
     * @param array                       $siteIds
     *
     * @return array
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration, array $siteIds);

    /**
     * @param array $siteIds
     *
     * @return int
     */
    public function count(array $siteIds);

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param array                       $siteIds
     *
     * @return int
     */
    public function countWithFilter(PaginateFinderConfiguration $configuration, array $siteIds);

    /**
     * @param array $groupIds
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function removeGroups(array $groupIds);

    /**
     * @param SiteInterface $site
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function softDeleteGroupsBySite(SiteInterface $site);

    /**
     * @param string $perimeterType
     * @param string $oldItem
     * @param string $newItem
     * @param string $siteId
     */
    public function updatePerimeterItem($perimeterType, $oldItem, $newItem, $siteId);

    /**
     * @param string $perimeterType
     * @param string $item
     * @param string $siteId
     */
    public function removePerimeterItem($perimeterType, $item, $siteId);
}
