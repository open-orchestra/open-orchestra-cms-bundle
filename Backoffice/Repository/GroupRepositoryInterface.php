<?php

namespace OpenOrchestra\Backoffice\Repository;

use OpenOrchestra\Pagination\Configuration\PaginationRepositoryInterface;

/**
 * Interface GroupRepositoryInterface
 */
interface GroupRepositoryInterface extends PaginationRepositoryInterface
{

    /**
     * Find all groups linked to a site
     *
     * @return array
     */
    public function findAllWithSite();

    /**
     * Find all groups linked to site with $id
     *
     * @param string $id   The site id
     * @return array
     */
    public function findAllWithSiteId($id);
}
