<?php

namespace OpenOrchestra\GroupBundle\Repository;

use OpenOrchestra\BackofficeBundle\Repository\GroupRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class GroupRepository
 */
class GroupRepository extends AbstractAggregateRepository implements GroupRepositoryInterface
{
    use PaginationTrait;
}
