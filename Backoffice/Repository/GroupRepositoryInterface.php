<?php

namespace OpenOrchestra\Backoffice\Repository;

use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;

/**
 * Interface GroupRepositoryInterface
 */
interface GroupRepositoryInterface
{

    /**
     * Find all groups linked to a site
     *
     * @return array
     */
    public function findAllWithSite();

    /**
     * Find all groups linked to site with $id
     *
     * @param string $id   The site id
     * @return array
     */
    public function findAllWithSiteId($id);

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param array|null                  $siteId
     *
     * @return array
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration, array $siteIds = null);

    /**
     * @param array|null $siteId
     *
     * @return int
     */
    public function count(array $siteIds = null);

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param array|null                  $siteId
     *
     * @return int
     */
    public function countWithFilter(PaginateFinderConfiguration $configuration, array $siteIds = null);
}
