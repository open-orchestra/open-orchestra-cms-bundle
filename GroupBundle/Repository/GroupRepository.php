<?php

namespace OpenOrchestra\GroupBundle\Repository;

use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class GroupRepository
 */
class GroupRepository extends AbstractAggregateRepository
{
    use PaginationTrait;
}
