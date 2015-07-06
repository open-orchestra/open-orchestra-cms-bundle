<?php

namespace OpenOrchestra\GroupBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\Pagination\MongoTrait\FilterTrait;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;

/**
 * Class GroupRepository
 */
class GroupRepository extends DocumentRepository
{
    use PaginationTrait;
    use FilterTrait;
}
