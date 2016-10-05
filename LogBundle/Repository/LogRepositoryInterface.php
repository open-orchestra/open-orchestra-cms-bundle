<?php

namespace OpenOrchestra\LogBundle\Repository;
use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;


/**
 * Class LogRepositoryInterface
 */
interface LogRepositoryInterface
{
    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration);

    /**
     * @param FinderConfiguration $configuration
     *
     * @return int
     */
    public function count(FinderConfiguration $configuration);

    /**
     * @param FinderConfiguration $configuration
     *
     * @return mixed
     */
    public function countWithFilter(FinderConfiguration $configuration);
}
