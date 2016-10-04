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
     * Find all groups linked to $siteId
     *
     * @param string $siteId
     * @return array
     */
    public function findAllWithSiteId($siteId);
}
