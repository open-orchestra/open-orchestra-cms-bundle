<?php

namespace OpenOrchestra\LogBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\Pagination\MongoTrait\FilterTrait;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;

/**
 * Class LogRepository
 */
class LogRepository extends DocumentRepository implements LogRepositoryInterface
{
    use FilterTrait;
    use PaginationTrait;
}
