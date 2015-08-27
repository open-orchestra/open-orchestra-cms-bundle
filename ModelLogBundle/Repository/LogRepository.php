<?php

namespace OpenOrchestra\ModelLogBundle\Repository;

use OpenOrchestra\LogBundle\Repository\LogRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class LogRepository
 */
class LogRepository extends AbstractAggregateRepository implements LogRepositoryInterface
{
    use PaginationTrait;
}
