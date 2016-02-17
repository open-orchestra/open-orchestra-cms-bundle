<?php

namespace OpenOrchestra\Backoffice\Repository;

use OpenOrchestra\Pagination\Configuration\PaginationRepositoryInterface;

/**
 * Interface GroupRepositoryInterface
 */
interface GroupRepositoryInterface extends PaginationRepositoryInterface
{

    /**
     * find all groups with a site
     *
     * @return array
     */
    public function findAllWithSite();
}
