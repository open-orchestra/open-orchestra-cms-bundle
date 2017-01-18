<?php

namespace OpenOrchestra\LogBundle\Repository;

use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;


/**
 * Class LogRepositoryInterface
 */
interface LogRepositoryInterface
{
    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration);

    /**
     * @return int
     */
    public function count();

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return int
     */
    public function countWithFilter(PaginateFinderConfiguration $configuration);
}
