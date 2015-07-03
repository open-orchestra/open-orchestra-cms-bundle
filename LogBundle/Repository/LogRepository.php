<?php

namespace OpenOrchestra\LogBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\Pagination\MongoTrait\PaginateTrait;

/**
 * Class LogRepository
 */
class LogRepository extends DocumentRepository implements LogRepositoryInterface
{
    use PaginateTrait;
}
