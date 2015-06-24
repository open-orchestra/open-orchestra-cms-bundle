<?php

namespace OpenOrchestra\LogBundle\Repository;

use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class LogRepository
 */
class LogRepository extends AbstractAggregateRepository implements LogRepositoryInterface
{
    use PaginationTrait;
}