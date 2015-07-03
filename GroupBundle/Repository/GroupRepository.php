<?php

namespace OpenOrchestra\GroupBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\Pagination\MongoTrait\PaginateTrait;

/**
 * Class GroupRepository
 */
class GroupRepository extends DocumentRepository
{
    use PaginateTrait;
}
